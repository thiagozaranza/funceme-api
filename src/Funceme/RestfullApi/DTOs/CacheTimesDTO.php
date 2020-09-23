<?php

namespace Funceme\RestfullApi\DTOs;

class CacheTimesDTO
{
    private $min_database_refresh_time;
    private $default_update_time;
    private $default_expiration_time;

    public function setMinDatabaseRefreshTime(int $seconds): CacheTimesDTO
    {
        $this->min_database_refresh_time = $seconds;
        return $this;
    }

    public function getMinDatabaseRefreshTime(): int
    {
        return $this->min_database_refresh_time;
    }

    public function setDefaultUpdateTime(int $seconds): CacheTimesDTO
    {
        $this->default_update_time = $seconds;
        return $this;
    }

    public function getDefaultUpdateTime(): int
    {
        return $this->default_update_time;
    }

    public function setDefaultExpirationTime(int $seconds): CacheTimesDTO
    {
        $this->default_expiration_time = $seconds;
        return $this;
    }

    public function getDefaultExpirationTime(): int
    {
        return $this->default_expiration_time;
    }

    public function toArray()
    {
        return [
            'min_database_refresh_time' => $this->getMinDatabaseRefreshTime(),
            'default_update_time'       => $this->getDefaultUpdateTime(),
            'default_expiration_time'   => $this->getDefaultExpirationTime()
        ];
    }
}