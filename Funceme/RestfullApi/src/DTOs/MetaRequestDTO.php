<?php

namespace Funceme\RestfullApi\DTOs;

class MetaRequestDTO
{
    private $URN;
    private $model;
    private $action = 'index';
    private $query_params;
    private $cache_options;
    private $oauth_info;

    public function __construct()
    {
        $this->setQueryParams(new QueryParamsDTO());
        $this->setCacheOptions(new CacheOptionsDTO());
    }

    public function setURN(string $URN): MetaRequestDTO
    {
        $this->URN = $URN;
        return $this;
    }

    public function getURN(): string {
        return $this->URN;
    }

    public function setModel(string $model): MetaRequestDTO
    {
        $this->model = $model;
        return $this;
    }

    public function getModel(): ?string {
        return $this->model;
    }

    public function setAction(string $action): MetaRequestDTO
    {
        $this->action = $action;
        return $this;
    }

    public function getAction(): string {
        return $this->action;
    }

    public function setQueryParams(QueryParamsDTO $query_params): MetaRequestDTO
    {
        $this->query_params = $query_params;
        return $this;
    }

    public function getQueryParams(): QueryParamsDTO 
    {
        return $this->query_params;
    }

    public function getQueryParam($param)
    {
        return $this->getQueryParams()->getParam($param);
    }

    public function getFilter(string $filter)
    {
        return $this->getQueryParams()->getFilter($filter);
    }

    public function addFilter(string $filter, $value)
    {
        return $this->getQueryParams()->addFilter($filter, $value);
    }

    public function orderBy(string $param, $direction = 'asc'): MetaRequestDTO
    {
        $this->getQueryParams()->orderBy($param, $direction);
        return $this;
    }

    public function limit($limit): MetaRequestDTO
    {
        $this->getQueryParams()->limit($limit);
        return $this;
    }

    public function getLimit(): ?int
    {
        return $this->getQueryParams()->getMetaPaginator()->getLimit();
    }

    public function page($page): MetaRequestDTO
    {
        $this->getQueryParams()->page($page);
        return $this;
    }

    public function addFilters(array $filters): MetaRequestDTO
    {
        $this->query_params->addFilters($filters);
        return $this;
    }

    public function with(array $with_list): MetaRequestDTO
    {
        $this->query_params->setWithList($with_list);
        return $this;
    }

    public function fetch(array $fetch_list): MetaRequestDTO
    {
        $this->query_params->setFetchList($fetch_list);
        return $this;
    }

    public function setCacheOptions($cache_options): MetaRequestDTO
    {
        $this->cache_options = $cache_options;
        return $this;
    }

    public function getCacheOptions(): CacheOptionsDTO 
    {
        return $this->cache_options;
    }

    public function setOAuthInfo($oauth_info): MetaRequestDTO
    {
        $this->oauth_info = $oauth_info;
        return $this;
    }

    public function getOAuthInfo(): ?OAuthInfoDTO 
    {
        return $this->oauth_info;
    }

    public function isPersonalToken()
    {
        if ($this->oauth_info && $this->oauth_info->getUserId())
            return true;
        
        return false;    
    }

    public function toArray()
    {
        return [
            'URN'           => $this->URN,
            'model'         => $this->model,
            'action'        => $this->action,
            'query_params'  => $this->query_params->toArray(),
            'cache_options' => $this->cache_options->toArray(),
            'oauth_info'    => ($this->oauth_info)? $this->oauth_info->toArray(): null
        ];
    }

    public function hash()
    {
        return md5(json_encode([
            $this->getModel(), 
            $this->getAction(), 
            $this->getQueryParams()->toArray()
        ]));
    }
}