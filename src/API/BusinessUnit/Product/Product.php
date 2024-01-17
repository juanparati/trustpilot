<?php

namespace Juanparati\Trustpilot\API\BusinessUnit\Product;

use GuzzleHttp\Exception\GuzzleException;
use Juanparati\Trustpilot\API\Resource;


class Product extends Resource
{
    /**
     * Save the product.
     *
     * @return Resource
     * @throws GuzzleException
     */
    public function save() : Resource
    {
        return (new ProductApi())->save([$this->toSaveArray()])[0];
    }

    /**
     * Get the parameters to save.
     *
     * @return array
     */
    private function toSaveArray(): array
    {
        return [
            'sku'                           => $this->sku,
            'title'                         => $this->title,
            'description'                   => $this->description,
            'mpn'                           => $this->mpn,
            'price'                         => $this->price,
            'currency'                      => $this->currency,
            'link'                          => $this->link,
            'imageLink'                     => $this->imageLink,
            'gtin'                          => $this->gtin,
            'brand'                         => $this->brand,
            'googleMerchantCenterProductId' => $this->googleMerchantCenterProductId,
        ];
    }
}
