<?php


namespace Palzin\Laravel\Tests;

use Palzin\Laravel\Providers\CommandServiceProvider;
use Palzin\Laravel\Providers\DatabaseQueryServiceProvider;
use Palzin\Laravel\Providers\EmailServiceProvider;
use Palzin\Laravel\Providers\GateServiceProvider;
use Palzin\Laravel\Providers\JobServiceProvider;
use Palzin\Laravel\Providers\NotificationServiceProvider;
use Palzin\Laravel\Providers\RedisServiceProvider;
use Palzin\Laravel\Providers\ExceptionServiceProvider;

class ContainerBindingTest extends BasicTestCase
{
    public function testBinding()
    {
        // Bind Palzin service
        $this->assertInstanceOf(\Palzin\Palzin::class, $this->app['palzin']);

        // Register service providers
        $this->assertInstanceOf(CommandServiceProvider::class, $this->app->getProvider(CommandServiceProvider::class));
        $this->assertInstanceOf(GateServiceProvider::class, $this->app->getProvider(GateServiceProvider::class));
        $this->assertInstanceOf(RedisServiceProvider::class, $this->app->getProvider(RedisServiceProvider::class));
        $this->assertInstanceOf(EmailServiceProvider::class, $this->app->getProvider(EmailServiceProvider::class));
        $this->assertInstanceOf(JobServiceProvider::class, $this->app->getProvider(JobServiceProvider::class));
        $this->assertInstanceOf(NotificationServiceProvider::class, $this->app->getProvider(NotificationServiceProvider::class));
        $this->assertInstanceOf(ExceptionServiceProvider::class, $this->app->getProvider(ExceptionServiceProvider::class));
        $this->assertInstanceOf(DatabaseQueryServiceProvider::class, $this->app->getProvider(DatabaseQueryServiceProvider::class));
    }
}
