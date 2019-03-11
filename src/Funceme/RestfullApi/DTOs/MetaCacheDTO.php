<?php

namespace Funceme\RestfullApi\DTOs;

use \Carbon\Carbon;

class MetaCacheDTO
{
    private $from_cache;
    private $expires_in;
    private $queue_in;
    private $built_in;
    private $cached_at;
    private $queued_at;

    public function __construct()
    {
        $this->from_cache   = false;
        $this->expires_in   = null;
        $this->queue_in     = null;
        $this->built_in     = null;
        $this->cached_at    = null;
        $this->queued_at    = null;
    }

    public function setFromCache($from_cache): MetaCacheDTO
    {
        $this->from_cache = $from_cache;
        return $this;
    }

    public function getFromCache(): bool
    {
        return $this->from_cache;
    }

    public function setExpiresIn($time_in_seconds): MetaCacheDTO
    {
        $this->expires_in = $time_in_seconds;
        return $this;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expires_in;
    }

    public function setQueueIn($time_in_seconds): MetaCacheDTO
    {
        $this->queue_in = $time_in_seconds;
        return $this;
    }

    public function getQueueIn(): ?int
    {
        return $this->queue_in;
    }

    public function setBuiltIn($time_in_microseconds): MetaCacheDTO
    {
        $this->built_in = $time_in_microseconds;
        return $this;
    }

    public function getBuiltIn(): float
    {
        return $this->built_in;
    }

    public function setCachedAt(?Carbon $cached_at): MetaCacheDTO
    {
        $this->cached_at = $cached_at;
        return $this;
    }

    public function getCachedAt(): ?Carbon
    {
        return $this->cached_at;
    }

    public function hasCachedAt(): bool
    {
        return ($this->cached_at == null)? false : true;
    }

    public function setQueuedAt(?Carbon $queued_at): MetaCacheDTO
    {
        $this->queued_at = $queued_at;
        return $this;
    }

    public function getQueuedAt(): ?Carbon
    {
        return $this->queued_at;
    }

    public function hasQueuedAt(): bool
    {
        return ($this->queued_at == null)? false : true;
    }
    
    public function toArray()
    {
        return [
            'from_cache'    => $this->getFromCache(),
            'expires_in'    => $this->getExpiresIn(),
            'queue_in'      => $this->getQueueIn(),
            'built_in'      => $this->getBuiltIn(),
            'cached_at'     => $this->getCachedAt(),
            'queued_at'     => $this->getQueuedAt()
        ];
    }
}