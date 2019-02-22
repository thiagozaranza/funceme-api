<?php

namespace Funceme\RestfullApi\Policies;

use Funceme\Models\Auth\User;
use Illuminate\Database\Eloquent\Model;

class BasePolicy
{
    protected $modelClass;

    public function __construct()
    {
        $this->modelClass = get_class(modelFactory($this));
    }

    /**
     * Determine if the given entity can be readed by the user.
     *
     * @param  App\Models\Auth\User  $user
     * @return bool
     */
    public function index(User $user)
    {
        if ($user->can('Read ' . $this->modelClass))
            return true;

        return false;
    }

    public function show(User $user, Model $obj)
    {   
        if ($user->can('Read ' . $this->modelClass))
            return true;

        return false;
    }
}
