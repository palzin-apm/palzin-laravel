<?php


namespace Palzin\Laravel\Tests;


use Palzin\Laravel\Providers\DatabaseQueryServiceProvider;
use Palzin\Laravel\Providers\EmailServiceProvider;
use Palzin\Laravel\Providers\JobServiceProvider;
use Palzin\Laravel\Providers\NotificationServiceProvider;
use Palzin\Laravel\Providers\RedisServiceProvider;
use Palzin\Laravel\Providers\ExceptionServiceProvider;

class DisablingProvidersTest extends BasicTestCase
{
    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $app['config']->set('palzin-apm.job', false);
        $app['config']->set('palzin-apm.query', false);
        $app['config']->set('palzin-apm.email', false);
        $app['config']->set('palzin-apm.notifications', false);
        $app['config']->set('palzin-apm.unhandled_exceptions', false);
        $app['config']->set('palzin-apm.redis', false);
    }

    public function testBindingDisabled()
    {
        // Bind Palzin service
        $this->assertInstanceOf(\Palzin\Palzin::class, $this->app['palzin']);

        // Nor register service providers
        $this->assertNull($this->app->getProvider(JobServiceProvider::class));
        $this->assertNull($this->app->getProvider(DatabaseQueryServiceProvider::class));
        $this->assertNull($this->app->getProvider(EmailServiceProvider::class));
        $this->assertNull($this->app->getProvider(NotificationServiceProvider::class));
        $this->assertNull($this->app->getProvider(ExceptionServiceProvider::class));
        $this->assertNull($this->app->getProvider(RedisServiceProvider::class));
    }
}
