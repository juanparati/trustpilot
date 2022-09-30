<?php

namespace Juanparati\Trustpilot\API;

use Juanparati\Trustpilot\API\Api;
use Juanparati\Trustpilot\Query\Queryable;

abstract class ResourceApi extends Api implements Queryable
{
    /**
     * Find the item from the id.
     *
     * @param string $id
     * @param array $params
     * @return mixed
     */
    public function find(string $id, array $params = [])
    {
        return $this->get('/' . $id, $params);
    }
}
