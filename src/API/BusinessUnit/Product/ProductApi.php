<?php

namespace Juanparati\Trustpilot\API\BusinessUnit\Product;

use GuzzleHttp\Exception\GuzzleException;
use Juanparati\Trustpilot\API\BusinessUnit\Review\Product\ImportedProductReviewApi;
use Juanparati\Trustpilot\API\BusinessUnit\Review\Product\ProductReviewApi;
use Juanparati\Trustpilot\API\BusinessUnit\Review\Product\ProductReviewSummary;
use Juanparati\Trustpilot\API\Resource;
use Juanparati\Trustpilot\API\ResourceApi;
use Juanparati\Trustpilot\Query\Builder;
use Illuminate\Support\Collection;

class ProductApi extends ResourceApi
{
    /**
     * The business unit id.
     *
     * @var
     */
    public ?string $businessUnitId = null;

    /**
     * Initialise the business unit product reviews with an optional business unit id.
     * If no business unit id is given, it uses the business unit from the config.
     *
     * @param string|null $businessUnitId
     */
    public function __construct(string $businessUnitId = null)
    {
        parent::__construct();
        $this->businessUnitId = $businessUnitId ?? config('trustpilot.unit_id');
    }

    /**
     * Perform the query and get the results.
     *
     * @param array $query
     * @param bool $search
     * @return Collection
     */
    public function perform(array $query, bool $search = false): Collection
    {
        $response = $this->get('/private/business-units/' . $this->businessUnitId . '/products', $query, true);
        return collect($response->products)->map(function ($product) {
            return (new Product())->data($product);
        });
    }

    /**
     * Save the products.
     *
     * @param array $products
     * @return Collection
     * @throws GuzzleException
     */
    public function save(array $products) : Collection
    {
        $response = $this->post('/private/business-units/' . $this->businessUnitId . '/products', [], [
            'products' => $products,
        ], true);

        return collect($response->products)->map(function ($product) {
            return (new Product())->data($product);
        });
    }

    /**
     * Get the queried product reviews.
     *
     * @return Builder
     */
    public function reviews(): Builder
    {
        return new Builder(new ProductReviewApi($this->businessUnitId));
    }

    /**
     * Get the joint products review summary.
     *
     * @param array $skus
     * @param array $urls
     * @return Resource
     * @throws GuzzleException
     */
    public function reviewSummary(array $skus = [], array $urls = []) : Resource
    {
        $response = $this->get('/product-reviews/business-units/' . $this->businessUnitId, array_filter([
            'sku' => $skus,
            'productUrl' => $urls,
        ]));

        return (new ProductReviewSummary())->data($response);
    }

    /**
     * Get the aggregated business product review summaries.
     *
     * @param array $skus
     * @param string $locale
     * @return Collection
     * @throws GuzzleException
     */
    public function reviewAggregatedSummaries(array $skus = [], string $locale = 'en-GB') : Collection
    {
        $response = $this->post('/product-reviews/business-units/' . $this->businessUnitId . '/attribute-summaries', [], array_filter([
            'skus' => $skus,
            'locale' => $locale,
        ]));

        return collect($response->summaries)->map(function ($summary) {
            return (new ProductReviewSummary())->data($summary);
        });
    }

    /**
     * Get the business product review summaries.
     *
     * @param array $skus
     * @return Collection
     * @throws GuzzleException
     */
    public function reviewBatchSummaries(array $skus = []) : Collection
    {
        $response = $this->post('/product-reviews/business-units/' . $this->businessUnitId . '/batch-summaries', [], array_filter([
            'skus' => $skus,
        ]));

        return collect($response->summaries)->map(function ($summary) {
            return (new ProductReviewSummary())->data($summary);
        });
    }

    /**
     * Get the queried imported product reviews.
     *
     * @return Builder
     */
    public function importedReviews(): Builder
    {
        return new Builder(new ImportedProductReviewApi($this->businessUnitId));
    }

    /**
     * Get the joint imported products review summary.
     *
     * @param array|string $sku
     * @return mixed
     * @throws GuzzleException
     */
    public function importedReviewSummary($sku) : Resource
    {
        if (is_array($sku)) {
            $sku = implode(',', $sku);
        }

        $response = $this->get('/product-reviews/business-units/' . $this->businessUnitId . '/imported-reviews-summaries', [
            'sku' => $sku,
        ]);

        return (new ProductReviewSummary())->data($response);
    }
}
