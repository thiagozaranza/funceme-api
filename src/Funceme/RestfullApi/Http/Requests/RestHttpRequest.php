<?php

namespace Funceme\RestfullApi\Http\Requests;

use Symfony\Component\HttpFoundation\Request;

use Funceme\RestfullApi\Http\Controllers\Controller;
use Funceme\RestfullApi\DTOs\CacheOptionsDTO;
use Funceme\RestfullApi\DTOs\QueryParamsDTO;
use Funceme\RestfullApi\DTOs\OAuthInfoDTO;
use Funceme\RestfullApi\DTOs\MetaRequestDTO;
use Funceme\RestfullApi\DTOs\MetaPaginatorDTO;

class RestHttpRequest extends Request
{
    public $request;

    protected $reserved_words = ['q','cache', 'with', 'orderby', 'page', 'fetch', 'limit', 'oauth_client_id', 'oauth_user_id'];
    protected $controller;
    protected $id;
 
    public function parse(Controller $controller, string $action, $id = null): MetaRequestDTO
    {   
        $this->controller = $controller;
        $this->id = $id;

        $this->request = request();

        $meta_request = new MetaRequestDTO;

        $urn = '';
        if (method_exists($this->request, 'getPathInfo'))
            $urn = $this->request->getPathInfo();
        else     
            $urn = $this->request->get('q');

        $meta_request->setURN($urn);
        $meta_request->setAction($action);
        $meta_request->setModel(get_class(modelFactory($this->controller)));
        $meta_request->setQueryParams($this->parseQueryParams());
        $meta_request->setCacheOptions($this->parseCacheOptions());
        $meta_request->setOAuthInfo($this->parseOAuthInfo());

        return $meta_request;
    }

    private function parseQueryParams(): QueryParamsDTO
    {   
        $params = new QueryParamsDTO();
        $params->setMetaPaginator($this->parseMetaPaginator());
        $params->setFilterList($this->parseFilterParams());
        $params->setWithList($this->parseWithList());
        $params->setFetchList($this->parseFetchList());

        return $params;
    }

    private function parseFilterParams(): array
    {
        $filters = [];

        $query_params = request()->toArray();

        if ($this->id) {
            $filters['id'] = $this->id; 
        } else {
            foreach ($query_params as $key => $value) {

                $key = strtolower($key);
    
                if (!in_array($key, $this->reserved_words)) {
    
                    if (strpos($value, ',')) {
                        $values = explode(',', $value);
                        sort($values);
                        $value = implode(',', $values);
                    }
                
                    $filters[$key] = $value;
                }
            }
        }

        return $filters;
    }

    private function parseMetaPaginator(): MetaPaginatorDTO
    {
        $meta_paginator = new MetaPaginatorDTO;

        $page       = ($this->request->has('page'))?    $this->request->input('page')     : 1;
        $limit      = ($this->request->has('limit'))?   $this->request->input('limit')    : 10;
        $order_by   = ($this->request->has('orderBy'))? $this->request->input('orderBy')  : null;
        
        $meta_paginator->setPage($page);
        $meta_paginator->setLimit($limit);
        $meta_paginator->setOrderBy($order_by);

        return $meta_paginator;
    }

    private function parseWithList(): array
    {
        return $this->request->has('with')? explode(',', $this->request->input('with')) : [];
    }

    private function parseFetchList(): array
    {
        return $this->request->has('fetch')? explode(',', $this->request->input('fetch')) : [];
    }

    private function parseOAuthInfo(): OAuthInfoDTO
    {
        $oauth = new OAuthInfoDTO;

        $client_id = ($this->request->has('oauth_client_id'))?  $this->request->input('oauth_client_id')    : '';
        $user_id   = ($this->request->has('oauth_user_id'))?    $this->request->input('oauth_user_id')      : '';

        $oauth->setClientId($client_id);
        $oauth->setUserId($user_id);

        return $oauth;   
    }

    private function parseCacheOptions(): CacheOptionsDTO
    {
        $cache_options = new CacheOptionsDTO();

        if (request()->has('cache') && request()->get('cache') == "no") {
            $cache_options->setUseCache(false);
        }

        $cache_control_header = request()->header("Cache-Control");

        while (strpos($cache_control_header, ' ')) {
            $cache_control_header = str_replace(' ', '', $cache_control_header);
        }

        $cache_control_parts = explode(',', $cache_control_header);

        foreach ($cache_control_parts as $cache_control_item) {
            switch($cache_control_item) {
                case 'no-cache':
                    $cache_options->setUseCache(true);
                    break;
                case 'no-store':
                    $cache_options->setNoStore(true);
                    break;
                case 'only-if-cached':
                    $cache_options->setOnlyIfCached(true);
                    break;
                case 'private':
                    $cache_options->setIsPublic(false);
                    break;
            }
            if (strpos($cache_control_item, 'max-age')!==false && strpos($cache_control_item, '=')!==false) {
                $cache_control_item_parts = explode('=', $cache_control_item);
                $max_age = $cache_control_item_parts[1];

                if (is_numeric($max_age))
                    $cache_options->setMaxAge(intval($max_age));
            }
        }

        return $cache_options;
    }
}
