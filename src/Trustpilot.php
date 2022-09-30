<?php

namespace Juanparati\Trustpilot;

use Juanparati\Trustpilot\API\BusinessUnit\BusinessUnit;
use Juanparati\Trustpilot\API\BusinessUnit\BusinessUnitApi;
use Juanparati\Trustpilot\API\BusinessUnit\Product\ProductApi;
use Juanparati\Trustpilot\API\Category\CategoryApi;
use Juanparati\Trustpilot\Query\Builder;

class Trustpilot
{

    /**
     * Get the default business unit.
     *
     * @param string|null $businessUnitId
     * @return \Juanparati\Trustpilot\API\BusinessUnit\BusinessUnit
     */
    public function businessUnit(?string $businessUnitId = null): BusinessUnit
    {
        return new BusinessUnit($businessUnitId);
    }

    /**
     * Get the business unit query builder.
     *
     * @return \Juanparati\Trustpilot\Query\Builder
     */
    public function businessUnits(): Builder
    {
        return new Builder(new BusinessUnitApi());
    }

    /**
     * Get the product query builder.
     *
     * @return \Juanparati\Trustpilot\Query\Builder
     */
    public function products(): Builder
    {
        return (new Builder(new ProductApi()))->setArrayAsComma();
    }

    /**
     * Get the category query builder.
     *
     * @return \Juanparati\Trustpilot\Query\Builder
     */
    public function categories(): Builder
    {
        return new Builder(new CategoryApi());
    }
}
