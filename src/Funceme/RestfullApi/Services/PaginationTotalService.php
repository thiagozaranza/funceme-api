<?php
namespace Funceme\RestfullApi\Services;

class PaginationTotalService extends BaseCacheableService
{
    public function doQuery()
    {
        return $this->getRepository()->getTotalResults(
            $this->getMetaRequest()->getQueryParams()
        );
    }

    public function getCacheTags(): array
    {
        return ['rest', 'total', $this->getMetaRequest()->getModel()];
    }
}
