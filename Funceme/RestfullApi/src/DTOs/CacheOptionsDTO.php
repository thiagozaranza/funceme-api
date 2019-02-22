<?php

namespace Funceme\RestfullApi\DTOs;

class CacheOptionsDTO
{
    private $is_public      = true;
    private $use_cache      = true;
    private $max_age        = -1;
    private $only_if_cached = false;
    private $no_store       = false;

    public function setIsPublic(bool $is_public): CacheOptionsDTO
    {
        $this->is_public = $is_public;
        return $this;
    }

    public function getIsPublic(): bool
    {
        return $this->is_public;
    }

    public function setUseCache(bool $use_cache): CacheOptionsDTO
    {
        $this->use_cache = $use_cache;
        return $this;
    }

    public function getUseCache(): bool
    {
        return $this->use_cache;
    }

    public function setMaxAge(int $max_age): CacheOptionsDTO
    {
        $this->max_age = $max_age;
        return $this;
    }

    public function getMaxAge(): int
    {
        return $this->max_age;
    }

    public function setOnlyIfCached(bool $only_if_cached): CacheOptionsDTO
    {
        $this->only_if_cached = $only_if_cached;
        return $this;
    }

    public function getOnlyIfCached(): bool
    {
        return $this->only_if_cached;
    }

    public function setNoStore(bool $no_store): CacheOptionsDTO
    {
        $this->no_store = $no_store;
        return $this;
    }

    public function getNoStore(): bool
    {
        return $this->no_store;
    }

    public function toArray()
    {
        return [
            'is_public'       => $this->getIsPublic(),
            'use_cache'       => $this->getUseCache(),
            'max_age'         => $this->getMaxAge(),
            'only_if_cached'  => $this->getOnlyIfCached(),
            'no_store'        => $this->getNoStore(),
        ];
    }
}
