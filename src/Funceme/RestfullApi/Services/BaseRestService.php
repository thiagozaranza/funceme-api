<?php
namespace Funceme\RestfullApi\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;

use Funceme\RestfullApi\DTOs\MetaRequestDTO;
use Funceme\RestfullApi\DTOs\CacheableObjectDTO;
use Funceme\RestfullApi\DTOs\PageDTO;

class BaseRestService
{
    /**
     * Instance of App\Repositories\BaseRepository
     */
    public $repository;

    /**
     *  Instance of App\DataTransferObjects\MetaRequestDTO
     */
    private $meta_request;

    public function __construct() 
    {
        $this->repository = repositoryFactory($this);
        
        $this->meta_request = new MetaRequestDTO();
        $this->meta_request->setModel($this->repository->getModelClass());
    }

    public function setMetaRequest(MetaRequestDTO $meta_request): BaseRestService
    {
        $this->meta_request = $meta_request;
        return $this;
    }

    public function getMetaRequest(): MetaRequestDTO
    {
        return $this->meta_request;
    }

    /**
    * Main method.
    *
    * @return CacheableObjectDTO
    **/
    public function get(): ?CacheableObjectDTO
    {
        $action = $this->getMetaRequest()->getAction();

        if (!method_exists($this, $action))
            throw new Exception("Action " . $action . "() não existente.");

        return $this->$action();
    }

    /**
    * Get a collection of objects
    *
    * @return PageDTO
    **/
    private function index(): CacheableObjectDTO
    {
        $cached_list = (new PaginationListService)
            ->setRepository($this->repository)
            ->setMetaRequest($this->meta_request)
            ->get();

        $cached_total = (new PaginationTotalService)
            ->setRepository($this->repository)
            ->setMetaRequest($this->meta_request)
            ->get();

        $limit = $this->meta_request->getLimit();

        $total_pages = ($limit)? ceil($cached_total->getData() / $limit) : 1;

        $page = new PageDTO;

        $page->setList($cached_list->getData());
        $page->setTotalResults($cached_total->getData());
        $page->setTotalPages($total_pages);

        $cacheable_object = $cached_list;
        $cacheable_object->setData($page);

        return $cacheable_object;
    }

    private function show(): CacheableObjectDTO
    {
        return (new ObjectService)
            ->setRepository($this->repository)
            ->setMetaRequest($this->meta_request)
            ->get();
    }

    public function getById($id) : CacheableObjectDTO
    {
        $this->meta_request->addFilter('id', $id);
        return $this->show();
    }

    public function store()
    {
        $model_class = $this->meta_request->getModel();

        if (!class_exists($model_class))
            throw new Exception('Entidade ' . $model_class . ' não existe.');

        $model = new $model_class;

        foreach ($this->meta_request->getFilters() as $key=>$value) {
            if (property_exists($model, 'maps') && array_key_exists($key, array_flip($model->maps)))
                $key = array_flip($model->maps)[$key];

            $model->$key = $value;
        }

        $this->repository->store($model);

        Cache::tags($model_class)->flush();
    
        return $model;
    }

    public function update()
    {
        $model_class = $this->meta_request->getModel();

        if (!class_exists($model_class))
            throw new Exception('Entidade ' . $model_class . ' não existe.');

        $filters = $this->meta_request->getFilters();

        $model = $model_class::findOrFail($filters['id']);

        foreach ($this->meta_request->getFilters() as $key=>$value) {
            if (property_exists($model, 'maps') && array_key_exists($key, array_flip($model->maps)))
                $key = array_flip($model->maps)[$key];

            $model->$key = $value;
        }

        $this->repository->update($model);

        Cache::tags($model_class)->flush();

        return $model;
    }

    public function destroy()
    {
        $filters = $this->meta_request->getFilters();
        $id = $filters['id'];

        $this->repository->destroy($id);

        $model_class = $this->meta_request->getModel();

        Cache::tags($model_class)->flush();
    }
}
