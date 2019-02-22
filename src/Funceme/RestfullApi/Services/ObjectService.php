<?php
namespace Funceme\RestfullApi\Services;

class ObjectService extends BaseCacheableService
{
    public function doQuery()
    {
        return $this->getRepository()->show(
            $this->getMetaRequest()->getFilter('id')
        );
    }

    public function getCacheTags(): array
    {
        return ['rest', 'show', $this->getMetaRequest()->getModel()];
    }
}
