<?php


namespace Palzin\Laravel\Providers;


use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\ServiceProvider;
use Palzin\Laravel\Facades\Palzin;
use Palzin\Laravel\Filters;
use Symfony\Component\Console\Input\ArgvInput;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $segments = [];

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {

        $this->app['events']->listen(CommandStarting::class, function (CommandStarting $event) {

            if (!$this->shouldBeMonitored($event->command)) {
                return;
            }


            if (Palzin::needTransaction()) {
                Palzin::startTransaction($event->command)
                    ->addContext('Command', [
                        'arguments' => $event->input->getArguments(),
                        'options' => $event->input->getOptions(),
                    ]);
            } elseif (Palzin::canAddSegments()) {
                $this->segments[$event->command] = Palzin::startSegment('artisan', $event->command);
            }
        });
        $this->app['events']->listen(CommandFinished::class, function (CommandFinished $event) {

            if (!$this->shouldBeMonitored($event->command)) {
                return;
            }
            if(Palzin::hasTransaction() && Palzin::currentTransaction()->name === $event->command) {
                Palzin::currentTransaction()->setResult($event->exitCode === 0 ? 'success' : 'error');
            } elseif(array_key_exists($event->command, $this->segments)) {
                $this->segments[$event->command]->end()->addContext('Command', [
                    'exit_code' => $event->exitCode,
                    'arguments' => $event->input->getArguments(),
                    'options' => $event->input->getOptions(),
                ]);
            }

        });
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

    /**
     * Determine if the current command should be monitored.
     *
     * @param string $command
     * @return bool
     */
    protected function shouldBeMonitored(?string $command): bool
    {
        if(is_string($command)) {
            return Filters::isApprovedArtisanCommand($command, config('palzin.ignore_commands'));
        }
        return false;
    }
}
