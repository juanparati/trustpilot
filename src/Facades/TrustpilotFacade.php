<?php

namespace Juanparati\Trustpilot\Facades;

use Juanparati\Trustpilot\Trustpilot;
use Illuminate\Support\Facades\Facade;

class TrustpilotFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Trustpilot::class;
    }
}
