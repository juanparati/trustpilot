<?php

namespace Juanparati\Trustpilot\API\Category;

use Juanparati\Trustpilot\API\Resource;
use Juanparati\Trustpilot\Query\Builder;

class Category extends Resource
{
    /**
     * Get the queried business units in the category.
     *
     * @return \Juanparati\Trustpilot\Query\Builder
     */
    public function businessUnits(): Builder
    {
        return new Builder(new BusinessUnit\BusinessUnitApi($this->categoryId));
    }

    /**
     * Load the category information.
     *
     * @return self
     */
    public function load()
    {
        return $this->data((new CategoryApi())->find($this->categoryId));
    }
}
