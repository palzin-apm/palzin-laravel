<?php

namespace Palzin\Laravel\Tests;


use Palzin\Laravel\Facades\Palzin;
use Palzin\Laravel\PalzinServiceProvider;
use Orchestra\Testbench\TestCase;

class BasicTestCase extends TestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [PalzinServiceProvider::class];
    }

    /**
     * Get package aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Palzin' => Palzin::class,
        ];
    }
}