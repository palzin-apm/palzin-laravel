<?php

namespace Palzin\Laravel;


use Illuminate\Contracts\View\Engine;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory as ViewFactory;
use Palzin\Laravel\Commands\TestCommand;
use Palzin\Laravel\Providers\CommandServiceProvider;
use Palzin\Laravel\Providers\DatabaseQueryServiceProvider;
use Palzin\Laravel\Providers\EmailServiceProvider;
use Palzin\Laravel\Providers\GateServiceProvider;
use Palzin\Laravel\Providers\JobServiceProvider;
use Palzin\Laravel\Providers\NotificationServiceProvider;
use Palzin\Laravel\Providers\RedisServiceProvider;
use Palzin\Laravel\Providers\ExceptionServiceProvider;
use Palzin\Laravel\Views\ViewEngineDecorator;
use Laravel\Lumen\Application as LumenApplication;
use Palzin\Configuration;
use Palzin\Laravel\Providers\HttpClientServiceProvider;
use Illuminate\Console\Scheduling\Schedule;


class PalzinServiceProvider extends ServiceProvider
{
    /**
     * The latest version of the client library.
     *
     * @var string
     */
    const VERSION = '24.8.1';

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {

        $this->setupConfigFile();


            $this->commands([
                TestCommand::class
            ]);

    }

    /**
     * Setup configuration file.
     */
    protected function setupConfigFile()
    {
        if ($this->app instanceof LaravelApplication) {
            $this->publishes([__DIR__ . '/../config/palzin-apm.php' => config_path('palzin-apm.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('palzin');
        }


    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Default package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/palzin-apm.php', 'palzin');

        // Bind Palzin service class
        $this->app->singleton('palzin', function ($app) {
            $configuration = (new Configuration(config('palzin-apm.key')))
                ->setEnabled(config('palzin-apm.enable', true))
                ->setUrl(config('palzin-apm.url', 'https://demo.palzin.app'))
                ->setVersion(self::VERSION)
                ->setTransport(config('palzin-apm.transport', 'async'))
                ->setOptions(config('palzin-apm.options', []))
                ->setMaxItems(config('palzin-apm.max_items', 100));

            return new Palzin($configuration);
        });

        $this->registerPalzinServiceProviders();


    }

    /**
     * Decorate View engine to monitor view rendering performance.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function bindViewEngine(): void
    {
        $viewEngineResolver = function (EngineResolver $engineResolver): void {
            foreach (['file', 'php', 'blade'] as $engineName) {
                $realEngine = $engineResolver->resolve($engineName);

                $engineResolver->register($engineName, function () use ($realEngine) {
                    return $this->wrapViewEngine($realEngine);
                });
            }
        };

        if ($this->app->resolved('view.engine.resolver')) {
            $viewEngineResolver($this->app->make('view.engine.resolver'));
        } else {
            $this->app->afterResolving('view.engine.resolver', $viewEngineResolver);
        }
    }

    private function wrapViewEngine(Engine $realEngine): Engine
    {
        /** @var ViewFactory $viewFactory */
        $viewFactory = $this->app->make('view');

        $viewFactory->composer('*', static function (View $view) use ($viewFactory): void {
            $viewFactory->share(ViewEngineDecorator::SHARED_KEY, $view->name());
        });

        return new ViewEngineDecorator($realEngine, $viewFactory);
    }

    /**
     * Bind Palzin service providers based on package configuration.
     */
    public function registerPalzinServiceProviders()
    {

        $this->app->register(CommandServiceProvider::class);


        $this->app->register(GateServiceProvider::class);

        // For Laravel >=6
        if (config('palzin-apm.redis', true) && version_compare(app()->version(), '6.0.0', '>=')) {
            $this->app->register(RedisServiceProvider::class);
        }


        if (config('palzin-apm.unhandled_exceptions', true)) {
            $this->app->register(ExceptionServiceProvider::class);
        }

        if(config('palzin-apm.query', true)){
            $this->app->register(DatabaseQueryServiceProvider::class);
        }

        if (config('palzin-apm.job', true)) {
            $this->app->register(JobServiceProvider::class);
        }

        if (config('palzin-apm.email', true)) {
            $this->app->register(EmailServiceProvider::class);
        }

        if (config('palzin-apm.notifications', true)) {
            $this->app->register(NotificationServiceProvider::class);
        }

        // Compatibility with Laravel < 8.4
        if (
            config('palzin-apm.http_client', true) &&
            class_exists('\Illuminate\Http\Client\Events\RequestSending') &&
            class_exists('\Illuminate\Http\Client\Events\ResponseReceived')
        ) {
            $this->app->register(HttpClientServiceProvider::class);
        }


        if (config('palzin-apm.views')) {
            $this->bindViewEngine();
        }
    }
}
