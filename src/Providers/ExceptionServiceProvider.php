<?php


namespace Palzin\Laravel\Providers;


use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Palzin\Laravel\Facades\Palzin;

class ExceptionServiceProvider extends ServiceProvider
{
    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        if (class_exists(MessageLogged::class)) {
            // starting from L5.4 MessageLogged event class was introduced
            // https://github.com/laravel/framework/commit/57c82d095c356a0fe0f9381536afec768cdcc072
            $this->app['events']->listen(MessageLogged::class, function ($log) {
                $this->handleExceptionLog($log->level, $log->message, $log->context);
            });
        } else {
            $this->app['events']->listen('illuminate.log', function ($level, $message, $context) {
                $this->handleExceptionLog($level, $message, $context);
            });
        }
    }

    protected function handleExceptionLog($level, $message, $context)
    {
        if (
            isset($context['exception']) &&
            ($context['exception'] instanceof \Throwable)
        ) {
            $this->reportException($context['exception']);
        }

        if ($message instanceof \Throwable) {
            $this->reportException($message);
        }

        if (Palzin::isRecording() && Palzin::hasTransaction()) {
            Palzin::currentTransaction()
                ->addContext('logs', array_merge(
                    Palzin::currentTransaction()->getContext()['logs'] ?? [],
                    [
                        compact('level', 'message')
                    ]
                ));
        }

    }

    protected function reportException(\Throwable $exception)
    {
        if (!Palzin::isRecording()) {
            return;
        }

        $this->app['palzin']->reportException($exception, false);

        $this->app['palzin']->currentTransaction()->setResult('error');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
