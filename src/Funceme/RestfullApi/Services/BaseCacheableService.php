<?php
namespace Funceme\RestfullApi\Services;

use \Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

use Funceme\RestfullApi\Repositories\BaseRepository;
use Funceme\RestfullApi\DTOs\CacheableObjectDTO;
use Funceme\RestfullApi\DTOs\MetaRequestDTO;
use Funceme\RestfullApi\DTOs\CacheTimesDTO;

/**
 * BaseCacheableService
 * 
 * Classe mãe de todos os services que podem ter seus resultados cacheados.
 * Contém informações que determinam o comportamento do cache e objetos que 
 * acessam o banco de dados e lidam com o cache.
 * 
 * Os métodos doQuery(), hash() e getCacheTags() devem ser implementados pelas chasses filhas.
 * 
 */

abstract class BaseCacheableService
{
    // Objeto responsável por lidar diretamente com a lógica do cache. 
    protected $cache_service;

    // Objeto contendo os parametros necessários para reproduzir a requisição.
    protected $meta_request;

    // Objeto com acesso ao banco de dados que fará a consulta, caso necessária.
    protected $repository;

    public $cache_times;

    public function __construct()
    {
        $this->cache_times = new CacheTimesDTO();
        $this->cache_service = new CacheService($this);
    }

    public function getCacheService(): CacheService
    {
        return $this->cache_service;
    }

    public function setMetaRequest(MetaRequestDTO $meta_request): BaseCacheableService
    {
        $this->meta_request = $meta_request;
        return $this;
    }

    public function modifyCacheTimes(CacheTimesDTO $cache_times): BaseCacheableService
    {
        $this->cache_times = $cache_times;
        return $this;
    }

    public function setExpirationTime(int $seconds): BaseCacheableService
    {
        $this->cache_times->setExpirationTime($seconds);
        return $this;
    }

    public function setUpdateTime(int $seconds): BaseCacheableService
    {
        $this->cache_times->setUpdateTime($seconds);
        return $this;
    }

    public function getMetaRequest(): MetaRequestDTO
    {
        if (!$this->meta_request)
            $this->meta_request = new MetaRequestDTO();

        return $this->meta_request;
    }

    public function setRepository(BaseRepository $repository): BaseCacheableService
    {
        $this->repository = $repository;
        return $this;
    }

    public function getRepository(): BaseRepository
    {
        return $this->repository;
    }

    protected function getQueryParam($parameter_name)
    {
        $filter_list = $this->getMetaRequest()->getQueryParams()->getFilterList();

        return (array_key_exists($parameter_name, $filter_list))? $filter_list[$parameter_name] : null;
    }

   /**
     * Método principal da classe. Retorna o objeto solicitado, 
     * seja do cahce ou do banco de dados.
     *
     * @return CacheableObjectDTO
     */
    public function get(): CacheableObjectDTO
    {
        if (method_exists($this, 'setCacheTimes'))
            $this->setCacheTimes();
            
        return $this->cache_service->get();
    }

    /**
     * Retorna da chave única que servirá de identificador no cache.
     * 
     * @return string
     */
    public function hash()
    {
        return $this->meta_request->hash();
    }

    /**
     * Implementação da consulta ao banco de dados através do objeto repository. 
     * 
     * @return collection
     */
    public abstract function doQuery();
    

    /**
     * Retorna uma lista de tags para referênciar a informação cacheada.
     */
    public abstract function getCacheTags(): array;
}
