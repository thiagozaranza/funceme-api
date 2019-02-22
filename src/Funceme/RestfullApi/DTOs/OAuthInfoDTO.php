<?php

namespace Funceme\RestfullApi\DTOs;

class OAuthInfoDTO
{
    private $client_id;
    private $user_id;

    public function setClientId($client_id): OAuthInfoDTO{
        $this->client_id = $client_id;
        return $this;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function setUserId($user_id): OAuthInfoDTO{
        $this->user_id = $user_id;
        return $this;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function toArray() {
        return [
            'client_id' => $this->getClientId(),
            'user_id' => $this->getUserId()
        ];
    }

    public function hash()
    {
        return md5(json_encode($this->toArray()));
    }
}