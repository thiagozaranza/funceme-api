<?php

namespace Funceme\RestfullApi\Commands;

use Illuminate\Console\Command;

class CreateEntityCommand extends Command
{
    protected $signature = 'entity:create';

    protected $description = 'Create a standard entity classes';

    public function __construct()
    {
        parent::__construct();
    } 

    public function handle()
    {
        $this->alert($this->signature);
    }
}
