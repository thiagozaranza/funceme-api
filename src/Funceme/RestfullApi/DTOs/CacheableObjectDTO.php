<?php

namespace Funceme\RestfullApi\DTOs;

use \Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class CacheableObjectDTO
{
    private $meta_request;
    private $meta_cache;
    private $data;

    public function __contruct()
    {
        $this->meta_request = new MetaRequestDTO;
        $this->meta_cache   = new MetaCacheDTO;
        $this->data         = null;
    }

    public function setMetaRequest(MetaRequestDTO $meta_request)
    {
        $this->meta_request = $meta_request;
        return $this;
    }

    public function getMetaRequest(): MetaRequestDTO
    {
        return $this->meta_request;
    }

    public function setMetaCache(MetaCacheDTO $meta_cache)
    {
        $this->meta_cache = $meta_cache;
        return $this;
    }

    public function getMetaCache(): MetaCacheDTO
    {
        return $this->meta_cache;
    }

    public function setData($data)
    {
        $data = $this->cleanCollection($data);

        //dd($data);

        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getList()
    {   
        $data = $this->getData();

        if (is_array($data) && array_key_exists('list', $data))
            return $data['list'];
        else if (is_object($data) && method_exists($data, 'getList'))    
            return $this->getData()->getList();
        else 
            return null;    
    }

    public function toArray()
    {
        return [
            'meta'  => [
                'request'   => $this->getMetaRequest()->toArray(),
                'cache'     => $this->getMetaCache()->toArray(),
            ], 
            'data'  => (is_array($this->getData()))? $this->getData() : $this->getData()->toArray(),
        ];
    }

    public function hash()
    {
        return md5(json_encode($this->getMetaRequest()->toArray()));
    }

    private function cleanCollection($object)
    {
        if (!(is_object($object) && $object instanceof Collection))
            return $object;

        $collection = new Collection;

        foreach ($object as $item) {
            $collection->push($this->cleanObject($item));
        }

        //dd($collection);

        return $collection;
    }

    private function cleanObject($object)
    {
        if (!$object instanceof Model || !property_exists($object, 'maps'))
            return $object;

        $properties = array_values($object->maps);

        foreach ($properties as $property) {

            $value = $object->$property;

            if ($value === null) {
                Log::info('unset ' . $property);
                unset($object[$property]);
            }    
            
            if (is_object($value) or is_array($value))
                $object->$property = $this->cleanObject($object->$property);
        }

        return $object;
    }
}
