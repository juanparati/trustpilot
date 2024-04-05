<?php

namespace Juanparati\Trustpilot\Tests;

use Juanparati\Trustpilot\Providers\TrustpilotServiceProvider;
use Juanparati\Trustpilot\Trustpilot;
use Illuminate\Foundation\AliasLoader;
use Orchestra\Testbench\TestCase;


abstract class TestBase extends TestCase
{

    /**
     * Load service providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return string[]
     */
    protected function getPackageProviders($app)
    {
        return [TrustpilotServiceProvider::class];
    }


    /**
     * Prepare the environment and configuration.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app) {
        // Credentials used for testing purposes are saved into ".secrets" directory.
        $config = json_decode(file_get_contents(__DIR__ . '/.secrets/config.json'), true);
        $app['config']->set('trustpilot', $config);

        $loader = AliasLoader::getInstance();
        $loader->alias('Trustpilot', \Juanparati\Trustpilot\Facades\TrustpilotFacade::class);
    }

    /**
     * Get instance.
     *
     * @return Trustpilot
     */
    protected function getInstance() : Trustpilot {
        return $this->app->make(Trustpilot::class);
    }

}
