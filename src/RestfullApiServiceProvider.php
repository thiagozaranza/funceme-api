<?php

namespace Funceme\RestfullApi;

use Illuminate\Support\ServiceProvider;

class RestfullApiServiceProvider extends ServiceProvider {

    public function boot()
    { 
        
    }
        
    public function register()    
    {
        $this->commands([
            Funceme\RestfullApi\Commands\CreateEntityCommand::class
        ]);
    }
}
