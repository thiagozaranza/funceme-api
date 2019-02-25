<?php
namespace Funceme\RestfullApi\Services;

use \Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use Funceme\RestfullApi\Repositories\BaseRepository;
use Funceme\RestfullApi\DTOs\CacheableObjectDTO;
use Funceme\RestfullApi\DTOs\MetaRequestDTO;

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
    // Tempo mínimo para que uma requisição consulte o banco de dados mesmo solicitando que o cache seja ignorado. (tempo em segundos)
    public $min_database_refresh_time = 60;       // default: 1 minute

    // Após o cache ficar com o tempo de criação maior que este valor, o cache será atualizado em background. (tempo em segundos)
    public $default_update_time       = 60*60*1;  // default: 1 hours

    // Tempo de vida da informação cacheada. (tempo em segundos)
    public $default_expiration_time   = 60*60*3; // default: 24 hours

    // Objeto responsável por lidar diretamente com a lógica do cache. 
    protected $cache_service;

    // Objeto contendo os parametros necessários para reproduzir a requisição.
    protected $meta_request;

    // Objeto com acesso ao banco de dados que fará a consulta, caso necessária.
    protected $repository;

    public function __construct()
    {
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

    public function setCacheTimes()
    {
        // Override if cache times can be changed.
    }

    /**
     * Método principal da classe. Retorna o objeto solicitado, 
     * seja do cahce ou do banco de dados.
     *
     * @return CacheableObjectDTO
     */
    public function get(): CacheableObjectDTO
    {
        $this->setCacheTimes();
        
        return $this->cache_service->get();
    }

    /**
     * Verifica se o Model do objeto da solicitação tem sua própria configuração 
     * de tempo de expiração ou retorna o valor default definido nesta classe.
     * 
     * @return int
     */
    public function getExpirationTime(): int
    {
        $expiration_time = $this->default_expiration_time;

        $model_class = $this->getMetaRequest()->getModel();
        
        if ($model_class) {
            $model = new $model_class;
            if (property_exists($model, 'expirationTime'))
                $expiration_time = $model::expirationTime;
        }

        return $expiration_time;
    }

    /**
     * Verifica se o Model do objeto da solicitação tem sua própria configuração 
     * de tempo de update ou retorna o valor default definido nesta classe.
     * 
     * @return int
     */
    public function getUpdateTime(): int
    {
        $update_time = $this->default_update_time;

        $model_class = $this->getMetaRequest()->getModel();

        if ($model_class) {
            $model = new $model_class;
            if (property_exists($model, 'updateTime'))
                $expiration_time = $model::updateTime;
        }

        return $update_time;
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
