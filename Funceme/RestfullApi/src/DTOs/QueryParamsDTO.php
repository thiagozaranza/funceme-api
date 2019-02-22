<?php

namespace Funceme\RestfullApi\DTOs;

class QueryParamsDTO
{
    private $meta_paginator;
    private $filter_list = [];
    private $with_list = [];
    private $fetch_list = [];

    public function __construct()
    {
        $this->setMetaPaginator(new MetaPaginatorDTO());
    }

    public function addFilters(array $filters): QueryParamsDTO
    {
        $this->filter_list = array_merge($filters, $this->filter_list);
        return $this;
    }

    public function orderBy($param, $direction = 'asc'): QueryParamsDTO
    {
        $this->getMetaPaginator()->setOrderBy($param . ', ' . $direction);

        return $this;
    }

    public function limit($limit): QueryParamsDTO
    {
        $this->getMetaPaginator()->setLimit($limit);

        return $this;
    }

    public function page($page): QueryParamsDTO
    {
        $this->getMetaPaginator()->setPage($page);

        return $this;
    }

    public function addFilter(string $key, $value): QueryParamsDTO
    {
        $this->filter_list[$key] = $value;
        return $this;
    }

    public function getFilter(string $param)
    {
        return $this->getParam($param);
    }

    public function getParam(string $param)
    {
        return (array_key_exists($param, $this->filter_list))?
            $this->filter_list[$param] : null;
    }

    public function setMetaPaginator(MetaPaginatorDTO $meta_paginator): QueryParamsDTO {
        $this->meta_paginator = $meta_paginator;
        return $this;
    }

    public function getMetaPaginator(): MetaPaginatorDTO {
        return $this->meta_paginator;
    }

    public function setFilterList($filter_list): QueryParamsDTO {
        $this->filter_list = $filter_list;
        return $this;
    }

    public function getFilterList(): array {
        return $this->filter_list;
    }

    public function setWithList($with_list): QueryParamsDTO {
        $this->with_list = $with_list;
        return $this;
    }

    public function getWithList(): array {
        return $this->with_list;
    }

    public function setFetchList($fetch_list): QueryParamsDTO {
        $this->fetch_list = $fetch_list;
        return $this;
    }

    public function getFetchList(): array {
        return $this->fetch_list;
    }

    public function toArray() {
        return [
            'paginator'     => $this->getMetaPaginator()->toArray(),
            'filter_list'   => $this->getFilterList(),
            'with_list'     => $this->getWithList(),
            'fetch_list'    => $this->getFetchList(),
        ];
    }
}
