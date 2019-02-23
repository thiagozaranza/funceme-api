<?php
namespace Funceme\RestfullApi\Repositories;

use Illuminate\Database\Eloquent\Builder as EloquentQueryBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Pagination\AbstractPaginator as Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Funceme\RestfullApi\Traits\PaginationTrait;

abstract class BaseRepository
{
    use PaginationTrait;

    /**
    * Model class for repo.
    *
    * @var string
    */
    protected $modelClass;

    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
    * @return EloquentQueryBuilder|QueryBuilder
    */
    protected function newQuery()
    {
      return app($this->modelClass)->newQuery();
    }

    /**
    * @param int        $id
    *
    * @return Array
    */
    public function show($id)
    {
        $obj = $this->findByID($id, true);

        foreach (get_class_methods($obj) as $_method) {

            if ($_method == '__construct')
                break;

            if (!is_object($obj->$_method()))
                continue;

            $_class = get_class($obj->$_method());

            if (strpos($_class, 'HasMany') !== false || strpos($_class, 'BelongsToMany') !== false) {

                $_obj = $obj->$_method()->get();

                /*var_dump($_obj);
                var_dump(get_class_methods($_obj)); exit;

                if (method_exists($obj->$_method()->getModel(), 'getPostgisFields')) {
                    var_dump($obj->$_method()->getModel()->getPostgisFields()); exit;
                    foreach ($obj->$_method()->getModel()->getPostgisFields() as $postgis_field) {
                        unset($_obj->$postgis_field);
                    }
                }*/
                //var_dump($obj->$_method()->getModel()); exit;

                $obj[$_method] = $_obj;
            } else if (strpos($_class, 'BelongsTo') !== false) {
                $_value = $obj->$_method()->get();
                if (count($_value) > 0)
                    $obj[$_method] = $_value[0];
            }
        }

        return $obj;
    }

    /**
    *
    *
    */
    public function store($obj)
    {
        //$obj->created_by = Auth::user()->id;

        $obj->save();

        foreach (get_class_methods($obj) as $_method) {

            if ($_method == '__construct')
                break;

            $_class = get_class($obj->$_method());

            if ((strpos($_class, 'HasMany') !== false || strpos($_class, 'BelongsToMany') !== false) && Input::get($_method))
                $obj->$_method()->sync(explode(',', Input::get($_method)));
        }

        return $obj;
    }

    /**
    *
    *
    */
    public function update($obj)
    {
        //$obj->updated_by = Auth::user()->id;

        $obj->save();

        foreach (get_class_methods($obj) as $_method) {

            if ($_method == '__construct')
                break;

            $_class = get_class($obj->$_method());

            if ((strpos($_class, 'HasMany') !== false || strpos($_class, 'BelongsToMany') !== false)
                && method_exists($obj, $_method)
                && method_exists($obj->$_method(), 'sync')) {

                if (!is_null(Input::get($_method)))
                    $obj->$_method()->sync(explode(',', Input::get($_method)));
                else
                    $obj->$_method()->sync([]);
            }
        }

        return $obj;
    }

    /**
    *
    *
    *
    */
    public function destroy($id)
    {
        $obj = call_user_func(array($this->modelClass, "find"), $id);
        $obj->destroy($id);
    }

    /**
    * Returns all records.
    * If $take is false then brings all records
    * If $paginate is true returns Paginator instance.
    *
    * @param int  $take
    * @param bool $paginate
    *
    * @return EloquentCollection|Paginator
    */
    public function getAll($take = 15, $paginate = true)
    {
        return $this->doQuery(null, $take, $paginate);
    }

    /**
    * @param string      $column
    * @param string|null $key
    *
    * @return \Illuminate\Support\Collection
    */
    public function lists($column, $key = null)
    {
        return $this->newQuery()->pluck($column, $key);
    }

    /**
    * Retrieves a record by his id
    * If fail is true $ fires ModelNotFoundException.
    *
    * @param int  $id
    * @param bool $fail
    *
    * @return Model
    */
    public function findByID($id, $fail = true)
    {
        if ($fail)
            return $this->newQuery()->findOrFail($id);

        return $this->newQuery()->find($id);
    }
}
