<?php


namespace Palzin\Laravel\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Config\Repository;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'palzin:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send data to your Palzin dashboard.';

    /**
     * Execute the console command.
     *
     * @param Repository $config
     * @return void
     * @throws \Throwable
     */
    public function handle(Repository $config)
    {
        if (!palzin()->isRecording()) {
            $this->warn('Palzin is not enabled');
            return;
        }

        $this->line("I'm testing your Palzin integration.");


        try {
            proc_open("", [], $pipes);
        } catch (\Throwable $exception) {
            $this->warn("❌ proc_open function disabled.");
            return;
        }
        // Check Palzin API key
        palzin()->addSegment(function ($segment) use ($config) {
            usleep(10 * 1000);

            !empty($config->get('palzin.key'))
                ? $this->info('✅ Palzin key installed.')
                : $this->warn('❌ Palzin key not specified. Make sure you specify the PALZIN_APM_INGESTION_KEY in your .env file.');

            $segment->addContext('example payload', ['key' => $config->get('palzin.key')]);
        }, 'test', 'Check Ingestion key');

        // Check Palzin is enabled
        palzin()->addSegment(function ($segment) use ($config) {
            usleep(10 * 1000);

            $config->get('palzin.enable')
                ? $this->info('✅ Palzin is enabled.')
                : $this->warn('❌ Palzin is actually disabled, turn to true the `enable` field of the `palzin` config file.');

            $segment->addContext('another payload', ['enable' => $config->get('palzin.enable')]);
        }, 'test', 'Check if Palzin is enabled');

        // Check CURL
        palzin()->addSegment(function ($segment) use ($config) {
            usleep(10 * 1000);

            function_exists('curl_version')
                ? $this->info('✅ CURL extension is enabled.')
                : $this->warn('❌ CURL is actually disabled so your app could not be able to send data to Palzin.');

            $segment->addContext('another payload', ['foo' => 'bar']);
        }, 'test', 'Check CURL extension');

        // Report Exception
        palzin()->reportException(new \Exception('First Exception detected'));
        // End the transaction
        palzin()->currentTransaction()
            ->setResult('success')
            ->end();

        // Demo data
        Log::debug("Here you'll find log entries generated during the transaction.");

        /*
         * Loading demo data
         */
        $this->line('Loading demo data...');


        foreach ([1, 2, 3, 4, 5, 6] as $minutes) {
            palzin()->startTransaction("Other transactions")
                ->start(microtime(true) - 60*$minutes)
                ->setResult('success')
                ->end(rand(100, 200));


            // Logs will be reported in the transaction context.
            Log::debug("Here you'll find log entries generated during the transaction.");
        }

        $this->line('Done!');
    }
}
