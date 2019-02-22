<?php

namespace Funceme\RestfullApi\DTOs;

use \Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;


class PageDTO
{
    private $total_pages = 0;
    private $total_results = 0;
    private $list;

    public function setTotalResults(int $total_results): PageDTO
    {
        $this->total_results = $total_results;
        return $this;
    }

    public function getTotalResults(): int
    {
        return $this->total_results;
    }

    public function setTotalPages(int $total_pages): PageDTO
    {
        $this->total_pages = $total_pages;
        return $this;
    }

    public function getTotalPages(): int
    {
        return $this->total_pages;
    }

    public function setList(Collection $list): PageDTO
    {
        $this->list = $list;
        return $this;
    }

    public function getList(): Collection
    {
        return $this->list;
    }

    public function toArray()
    {
        $result = [
            'total_results' => $this->getTotalResults(),
            'total_pages'   => $this->getTotalPages(),
            'list'          => $this->getList(),
        ];

        return $result;
    }

    public function hash()
    {
        return md5(json_encode($meta));
    }
}
