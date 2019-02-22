<?php

namespace Funceme\RestfullApi\Models\Auth;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $connection = 'fauno-auth';

    protected $table = 'api.users';

    protected $hidden = ['password', 'remember_token'];

    protected $fillable = ['name', 'email', 'password'];
}
