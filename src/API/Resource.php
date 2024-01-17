<?php

namespace Juanparati\Trustpilot\API;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Support\Arrayable;

class Resource implements Arrayable, \JsonSerializable
{
    /**
     * Resource data.
     *
     * @var array
     */
    protected array $data = [];

    /**
     * Initalise the resource.
     *
     * @param array $data The resource data.
     */
    public function __construct(array $data = [])
    {
        $this->data($data);
    }

    /**
     * Set the data.
     *
     * @param $data
     * @return $this
     */
    public function data($data): static
    {
        foreach ($data as $key => $value) {
            // Handle dates
            if ($key == 'createdAt' || $key == 'updatedAt') {
                $value = CarbonImmutable::parse($value);
            }

            $this->data[$key] = $value;
        }
        return $this;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return json_decode($this->jsonSerialize(), true);
    }


    /**
     * Retrieve resource property (Magic method).
     *
     * @param string $name
     * @return mixed
     */
    public function __get(string $name) : mixed
    {
        return $this->data[$name] ?? null;
    }


    /**
     * Set specific resource property (Magic method).
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, mixed $value) : void
    {
        $this->data[$name] = $value;
    }


    /**
     * Serialize resource.
     *
     * @return array
     */
    public function __serialize() : array
    {
        return $this->toArray();
    }

    /**
     * Unserialize resource.
     *
     * @param array $data
     * @return void
     */
    public function __unserialize(array $data) : void
    {
        $this->data($data);
    }

    /**
     * Serialize resource.
     *
     * @return string
     */
    public function jsonSerialize() : string
    {
        return json_encode($this->data);
    }
}
