<?php


namespace Palzin\Laravel\Providers;


use Illuminate\Log\Events\MessageLogged;
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
            $this->app['events']->listen(MessageLogged::class, function (MessageLogged $log) {
                $this->handleExceptionLog($log->level, $log->message, $log->context);
            });
        } else {
            $this->app['events']->listen('illuminate.log', function ($level, $message, $context) {
                $this->handleExceptionLog($level, $message, $context);
            });
        }
    }

    /**
     * Attach the event to the current transaction.
     *
     * @param string $level
     * @param mixed $message
     * @param mixed $context
     * @return void
     */
    protected function handleExceptionLog($level, $message, $context)
    {

        if (
            isset($context['exception']) &&
            ($context['exception'] instanceof \Throwable)
        ) {
            return $this->reportException($context['exception']);
        }

        if ($message instanceof \Throwable) {
            return $this->reportException($message);
        }

        if (Palzin::hasTransaction()) {
            Palzin::transaction()
                ->addContext('logs', array_merge(
                    Palzin::transaction()->getContext()['logs'] ?? [],
                    [
                        compact('level', 'message')
                    ]
                ));
        }

    }

    protected function reportException(\Throwable $exception)
    {
        Palzin::reportException($exception, false);

        Palzin::transaction()->setResult('error');
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
