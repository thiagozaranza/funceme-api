<?php

namespace Funceme\RestfullApi\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

use Funceme\Repositories\BaseRepository;
use Funceme\DTOs\CacheableObjectDTO;
use Funceme\DTOs\CacheOptionsDTO;
use Funceme\Services\PaginationService;

class UpdateCacheJob implements ShouldQueue
{
    use InteractsWithQueue;

    public $tries = 3;

    public $timeout = 30;

    private $cacheable_service;
    private $cached_object;

    public function __construct($cacheable_service, CacheableObjectDTO $cached_object)
    {
        $this->cacheable_service  = $cacheable_service;
        $this->cached_object = $cached_object;

        if ($this->cached_object) {
            $this->cached_object->getMetaCache()->setQueuedAt(\Carbon\Carbon::now());
            $cacheable_service->getCacheService()->updateCache($this->cached_object);
        }
    }

    public function handle()
    {
        $object = $this->cacheable_service->getCacheService()->getFromDatabase(true);

        $this->cacheable_service->getCacheService()->updateCache($object);
    }
}
