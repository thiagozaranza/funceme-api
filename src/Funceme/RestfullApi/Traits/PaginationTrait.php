<?php

namespace Funceme\RestfullApi\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\Collection;

use Funceme\RestfullApi\DTOs\QueryParamsDTO;

trait PaginationTrait
{
    private $operators = ['-eq', '-neq', '-lk', '-gt', '-gte', '-lt', '-lte', '-or', 'oauth_client_id', 'oauth_user_id'];

    private function buildQuery(&$query, QueryParamsDTO $paginator)
    { 
        // For each filter, do the sql build
        foreach ($paginator->getFilterList() as $filter_key => $filter_value) {

            $filter_field = null;
            $op = 'eq';

            $filter_key = strtolower($filter_key);

            // operador existe: nome-eq=1
            if (str_contains($filter_key, $this->operators)) {
                $key_parts = explode('-',$filter_key);
                $op = array_pop($key_parts);
            }
            //operador não existe o objeto com relacionamento escola-nome=1
            else if (str_contains($filter_key, '-')) {
                $key_parts = explode('-',$filter_key);
            }
            // operador não existe e objeto simples: escola_id=1
            else {
                $key_parts = [$filter_key];
            }

            if (sizeof($key_parts) == 0)
                continue;

            $main_table   = $_table = $query->getModel()->getTable();
            $main_model   = $_model = $query->getModel();
            $filter_field = $_field = $key_parts[0];

            $query->select($main_table . '.*');

            // Do necessary joins with the filters parts
            while (sizeof($key_parts) > 0) {

                $filter_part = $_field = array_shift($key_parts);

                if (!method_exists($_model, $filter_part))
                    continue;

                $relation_type = get_class($_model->$filter_part());

                if ($relation_type == "Illuminate\Database\Eloquent\Relations\BelongsTo") {

                    if (property_exists($_model, 'maps') && array_key_exists($filter_part, array_flip($_model->maps)))
                        $filter_part_fk = array_flip($_model->maps)[$filter_part];
                    else
                        $filter_part_fk = $filter_part . '_id';

                    $_model = $_model->$filter_part()->getModel();
                    $_table = $_model->getTable();
                    $_primaryKey = ($_model->primaryKey)? $_model->primaryKey : 'id';

                    $joins = $query->getQuery()->joins;

                    $isJoined = false;

                    if ($joins) {
                        foreach ($joins as $join) {
                            if ($join->table == $_table)
                                $isJoined = true;
                        }
                    }

                    if (!$isJoined)
                        $query->join($_table, $filter_part_fk, '=', $_table . '.' . $_primaryKey);

                    if (sizeof($key_parts) == 1) {
                        if (property_exists($_model, 'maps') && array_key_exists($key_parts[0], array_flip($_model->maps))) {
                            $_field = array_flip($_model->maps)[$key_parts[0]];
                        } else {
                            $_field = $key_parts[0];
                        } 

                    } else {
                        $_field = $_primaryKey;
                    }

                } elseif ($_type == 'Illuminate\Database\Eloquent\Relations\BelongsToMany') {

                    $_pivotTable = $_model->$_field()->wherePivot($_field)->getTable();
                    $_pivotForeignKeyName = $_model->$_field()->getQualifiedForeignKeyName();
                    $_pivotRelatedKeyName = $_model->$_field()->getQualifiedRelatedKeyName();

                    $query->join($_pivotTable, $_pivotForeignKeyName, '=', $_table . '.id');

                    $__model = $_model->$_field()->getModel();
                    $__table = $__model->getTable();

                    $query->join($__table, $_pivotRelatedKeyName, '=', $__table . '.id');
                }
            }

            if (property_exists($_model, 'maps') && is_array($_model->maps) && array_key_exists($_field, array_flip($_model->maps)))
                $_field = array_flip($_model->maps)[$_field];

            switch ($op) {
                case 'eq':
                    $query->where($_table . '.' . $_field, '=', $filter_value);
                    break;
                case 'neq':
                    $query->where($_table . '.' . $_field, '!=', $filter_value);
                    break;
                case 'lk':
                    $query->where($_table . '.' . $_field, 'ilike', '%' . $filter_value . '%');
                    break;
                case 'gt':
                    $query->where($_table . '.' . $_field, '>', $filter_value);
                    break;
                case 'gte':
                    $query->where($_table . '.' . $_field, '>=', $filter_value);
                    break;
                case 'lt':
                    $query->where($_table . '.' . $_field, '<', $filter_value);
                    break;
                case 'lte':
                    $query->where($_table . '.' . $_field, '<=', $filter_value);
                    break;
                case 'or':
                    foreach (explode(',', $value) as $filter_value) {
                        $query->orWhere($_table . '.' . $_field, '=', $filter_value);
                    }
                    $query->distinct($_table . '.id');
                    break;
            }
        }

        foreach ($paginator->getWithList() as $with_item) {

            $main_model = $query->getModel();

            $nested_parts = explode('-', $with_item);

            if (sizeof($nested_parts) > 1) {
                
                $last_nested_part = $nested_parts[sizeof($nested_parts) - 1];

                $nested_model = $main_model;

                foreach ($nested_parts as $nested_part) {
                    $nested_model = $nested_model->$nested_part()->getRelated();
                }

                $_with = implode('.', $nested_parts);
                
            } else {

                $_withParts = explode('.', $with_item);

                $eager_model_name = array_shift($_withParts);

                $eager_model = $main_model->$eager_model_name()->getRelated();

                $primaryKey = ($eager_model->primaryKey)? $eager_model->primaryKey : 'id';

                $fliped_map_keys = (property_exists($eager_model, 'maps'))? array_flip($eager_model->maps) : [];

                if (sizeof($_withParts) > 0) {

                    $_keys = [];

                    foreach (explode(';', $_withParts[0]) as $_key_part) {
                        if (property_exists($eager_model, 'maps'))
                            $_keys[] = $fliped_map_keys[$_key_part];
                        else 
                            $_keys[] = $_key_part;
                    }

                    if (property_exists($eager_model, 'maps')) {
                        if (array_key_exists('nome', $fliped_map_keys)) {
                            $name_key = $fliped_map_keys['nome'];
                            if (!array_key_exists($name_key, $_keys))
                                $_keys[] = $name_key;
                        }
                    } else {
                        $_keys[] = 'nome';
                    }

                    $_with = $eager_model_name . ':' . $primaryKey . ',' . implode(',', $_keys);  

                } else {
                    
                    $name_key = '';

                    if (property_exists($eager_model, 'maps') || (method_exists($main_model, $eager_model_name) && get_class($main_model->$eager_model_name()) == 'Illuminate\Database\Eloquent\Relations\HasMany')) {
                        $_with = $eager_model_name;
                    } else {
                        if (array_key_exists('nome', $fliped_map_keys)) 
                            $name_key =  ',' . $fliped_map_keys['nome'];
                        else if (property_exists($main_model, 'nameKey'))
                            $name_key = ','.$main_model->nameKey;
                        else
                            $name_key =  ',nome';

                        $_with = $eager_model_name . ':' . $primaryKey . $name_key;
                    }
                }
            }
 
            $query->with($_with);
        }
    }

    public function getTotalResults($paginator): int
    {
        $query = (new static)->newQuery();

        $this->buildQuery($query, $paginator);

        return $query->count();
    }

    public function paginate(QueryParamsDTO $paginator): Collection
    {
        $query = (new static)->newQuery();
        $model = $query->getModel();

        $this->buildQuery($query, $paginator);

        $limit = $paginator->getMetaPaginator()->getLimit();
        $offset = ($limit)? ($paginator->getMetaPaginator()->getPage() - 1) * $limit : 0;
        $order_by = $paginator->getMetaPaginator()->getOrderBy();

        if ($order_by) {

            $orderParts = explode(',', $order_by);

            $field = $orderParts[0];
            $mode = (sizeof($orderParts) > 1)? $orderParts[1] : 'asc';

            if (property_exists($model, 'maps') && array_key_exists($field, array_flip($model->maps))) 
                $field = array_flip($model->maps)[$field];

            $query->orderBy($field, $mode);
        }

        if ($limit)
            $query->limit($limit);

        $list = $query->offset($offset)->get();

        if (method_exists($query->getModel(), 'getPostgisFields')) {
            foreach ($list as &$item) {
                foreach ($query->getModel()->getPostgisFields() as $postgis_field) {
                    if (!in_array($postgis_field, $paginator->getFetchList()))
                        $item->addHidden($postgis_field);
                }
            }
        }

        return $list;
    }
}
