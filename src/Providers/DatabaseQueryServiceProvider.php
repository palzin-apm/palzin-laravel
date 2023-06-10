<?php


namespace Palzin\Laravel\Providers;


use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\ServiceProvider;
use Palzin\Laravel\Facades\Palzin;

class DatabaseQueryServiceProvider extends ServiceProvider
{
    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['events']->listen(QueryExecuted::class, function (QueryExecuted $query) {
            if (Palzin::canAddSegments()) {
                $this->handleQueryReport($query->sql, $query->bindings, $query->time, $query->connectionName);
            }
        });
    }

    /**
     * Attach a span to monitor query execution.
     *
     * @param $sql
     * @param array $bindings
     * @param $time
     * @param $connection
     */
    protected function handleQueryReport($sql, array $bindings, $time, $connection)
    {
        $segment = Palzin::startSegment($connection, substr($sql, 0, 50))
            ->start(microtime(true) - $time/1000);

        $context = [
            'connection' => $connection,
            'query' => $sql,
        ];

        if (config('palzin.bindings')) {
            $context['bindings'] = $bindings;
        }

        $segment->addContext('db', $context)->end($time);
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
