
# New resouce tutorial

## 1. Model*

- Create file app\\Models\\<Schemma ?>\\<Resource>.php
    - Extends Illuminate\Database\Eloquent\Model
    - Set table name
    - Define fillable and hidden fields
    - Configure relationships

## 2. Repository

- Create file app\\Repositories\\<Schemma ?>\\<Resource>Repository.php
    - Extends Funceme\RestfullApi\Repositories\BaseRepository
    - Set protected property $modelClass = <Resource>::class

## 3. Controller

- Create file app\\Http\\Controllers\\Rest\\<Schemma ?>\\<Resource>Controller.php
    - Extends Funceme\RestfullApi\Http\Controllers\Controller
    
## 4. Service

- Create file app\\Services\\Rest\\<Schemma ?>\\<Resource>Service.php
    - Extends Funceme\RestfullApi\Services\BaseRestService

## 5. Policy

- Create file app\\Policies\\<Resource>Policy.php
    - Extends Funceme\RestfullApi\Policies\BasePolicy
    
## 6. Register Policy
    
- Register policy on app/Providers/AuthServiceProvider.php    
    
## 7. Configure route
    
- Configure route on file app/routes/web.php
    - Declare router as resouce under the rest group
    
## 8. Configure permissions

- Configure permissions on file app/config/permission.php
    - Declare permissions on respectives roles
    - Run "php artisan roles:sync" command

    
