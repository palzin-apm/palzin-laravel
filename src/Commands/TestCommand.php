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
    protected $description = 'Send data to your Palzin Monitor (APM) dashboard.';

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
            $this->warn('Palzin Monitor (APM) ingestion is not enabled');
            return;
        }

        $this->line("I'm testing your Palzin Monitor (APM) integration.");


        try {
            proc_open("", [], $pipes);
        } catch (\Throwable $exception) {
            $this->warn("❌ proc_open function disabled.");
            return;
        }
        // Check Palzin API key
        palzin()->addSegment(function ($segment) use ($config) {
            usleep(10 * 1000);

            !empty($config->get('palzin-apm.key'))
                ? $this->info('✅ Palzin Monitor (APM) ingestion key installed.')
                : $this->warn('❌ Palzin Monitor (APM) ingestion key not specified. Make sure you specify the PALZIN_APM_INGESTION_KEY in your .env file.');

            $segment->addContext('example payload', ['key' => $config->get('palzin-apm.key')]);
        }, 'test', 'Check Palzin Monitor (APM) Ingestion key');

        // Check Palzin is enabled
        palzin()->addSegment(function ($segment) use ($config) {
            usleep(10 * 1000);

            $config->get('palzin-apm.enable')
                ? $this->info('✅ Palzin Monitor (APM) is enabled.')
                : $this->warn('❌ Palzin Monitor (APM) is actually disabled, turn to true the `enable` field of the `palzin-apm` config file.');

            $segment->addContext('another payload', ['enable' => $config->get('palzin-apm.enable')]);
        }, 'test', 'Check if Palzin Monitor (APM) is enabled');

        // Check CURL
        palzin()->addSegment(function ($segment) use ($config) {
            usleep(10 * 1000);

            function_exists('curl_version')
                ? $this->info('✅ CURL extension is enabled.')
                : $this->warn('❌ CURL is actually disabled so your app could not be able to send data to Palzin Monitor (APM).');

            $segment->addContext('another payload', ['foo' => 'bar']);
        }, 'test', 'Check CURL extension');

        // Report Exception
        palzin()->reportException(new \Exception('First Exception detected using Palzin Monitor (APM)'));
        // End the transaction
        palzin()->currentTransaction()
            ->setResult('success')
            ->end();

        // Demo data
        Log::debug("In this section, you can access the log entries that were created throughout the transaction.");

        /*
         * Loading demo data
         */
        $this->line('Loading demo data...');


        foreach ([1, 2, 3, 4, 5, 6] as $minutes) {
            palzin()->startTransaction("Other sample transactions")
                ->start(microtime(true) - 60*$minutes)
                ->setResult('success')
                ->end(rand(100, 200));


            // Logs will be reported in the transaction context.
            Log::debug("In this section, you can access the log entries that were created throughout the transaction.");
        }

        $this->line('Done!');
    }
}
