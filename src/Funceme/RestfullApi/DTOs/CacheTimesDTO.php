<?php

namespace Funceme\RestfullApi\DTOs;

use Illuminate\Support\Facades\Config;

class CacheTimesDTO
{
    private $min_database_refresh_time;
    private $update_time;
    private $expiration_time;

    public function __construct()
    {
        $this->min_database_refresh_time    = Config::get('cache.min_database_refresh_time');
        $this->update_time          = Config::get('cache.default_update_time');
        $this->expiration_time      = Config::get('cache.default_expiration_time');
    }

    public function setMinDatabaseRefreshTime(int $seconds): CacheTimesDTO
    {
        $this->min_database_refresh_time = $seconds;
        return $this;
    }

    public function getMinDatabaseRefreshTime(): int
    {
        return $this->min_database_refresh_time;
    }

    public function setUpdateTime(int $seconds): CacheTimesDTO
    {
        $this->update_time = $seconds;
        return $this;
    }

    public function getUpdateTime(): int
    {
        return $this->update_time;
    }

    public function setExpirationTime(int $seconds): CacheTimesDTO
    {
        $this->expiration_time = $seconds;
        return $this;
    }

    public function getExpirationTime(): int
    {
        return $this->expiration_time;
    }

    public function toArray()
    {
        return [
            'min_database_refresh_time' => $this->getMinDatabaseRefreshTime(),
            'update_time'       => $this->getUpdateTime(),
            'expiration_time'   => $this->getExpirationTime()
        ];
    }
}
