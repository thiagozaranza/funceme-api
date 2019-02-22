<?php
namespace Funceme\RestfullApi\Services;

class PaginationListService extends BaseCacheableService
{
    public function doQuery()
    {
        return $this->getRepository()->paginate(
            $this->getMetaRequest()->getQueryParams()
        );
    }

    public function getCacheTags(): array
    {
        return ['rest', 'list', $this->getMetaRequest()->getModel()];
    }
}
