<?php

namespace Funceme\RestfullApi\DTOs;

class MetaPaginatorDTO
{
    private $order_by;
    private $limit;
    private $page;

    public function setOrderBy(?string $order_by): MetaPaginatorDTO{
        $this->order_by = $order_by;
        return $this;
    }

    public function getOrderBy(): ?string {
        return $this->order_by;
    }

    public function setLimit($limit): MetaPaginatorDTO {

        if ($limit == 9999999 || $limit == 0 || $limit == 'no')
            $limit = null;
            
        $this->limit = $limit;
        return $this;
    }

    public function getLimit(): ?int {
        return $this->limit;
    }

    public function setPage($page): MetaPaginatorDTO {
        $this->page = $page;
        return $this;
    }

    public function getPage(): ?int {
        return $this->page;
    }

    public function toArray() {
        return [
            'order_by'      => $this->getOrderBy(),
            'limit'         => $this->getLimit(),
            'page'          => $this->getPage()           
        ];
    }
}
