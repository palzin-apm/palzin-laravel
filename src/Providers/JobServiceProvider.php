<?php


namespace Palzin\Laravel\Providers;



use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Support\ServiceProvider;
use Palzin\Laravel\Facades\Palzin;
use Palzin\Laravel\Filters;
use Palzin\Models\Segment;

class JobServiceProvider extends ServiceProvider
{
    /**
     * Jobs to inspect.
     *
     * @var Segment[]
     */
    protected $segments = [];

    /**
     * Booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // This event is never called in Laravel Vapor.
        // Queue::looping(
        //     function () {
        //         $this->app['palzin']->flush();
        //     }
        // );

        $this->app['events']->listen(
            JobProcessing::class,
            function (JobProcessing $event) {

                if ($this->shouldBeMonitored($event->job->resolveName())) {
                    $this->handleJobStart($event->job);
                }

                
            }
        );

        $this->app['events']->listen(
            JobProcessed::class,
            function (JobProcessed $event) {
                if ($this->shouldBeMonitored($event->job->resolveName()) && !$event->job->hasFailed()) {
                    $this->handleJobEnd($event->job);
                }
            }
        );

        $this->app['events']->listen(
            JobFailed::class,
            function (JobFailed $event) {
                if ($this->shouldBeMonitored($event->job->resolveName())) {
                    $this->handleJobEnd($event->job, true);
                }
            }
        );

    }

    

    protected function handleJobStart(Job $job)
    {

        if (Palzin::needTransaction()) {
            Palzin::startTransaction($job->resolveName())
                ->addContext('Payload', $job->payload());
        } elseif (Palzin::canAddSegments()) {
            $this->initializeSegment($job);
        }
    }

    protected function initializeSegment(Job $job)
    {
        $segment = Palzin::startSegment('job', $job->resolveName())
            ->addContext('Payload', $job->payload());

        // Save the job under a unique ID
        $this->segments[$this->getJobId($job)] = $segment;
    }

    /**
     * Finalize the monitoring of the job.
     *
     * @param Job $job
     * @param bool $failed
     */
    public function handleJobEnd(Job $job, $failed = false)
    {

        $id = $this->getJobId($job);

        // If a segment doesn't exists it means that job is registered as transaction
        // we can set the result accordingly
        if (array_key_exists($id, $this->segments)) {
            $this->segments[$id]->end();
        } else {
            Palzin::currentTransaction()
                ->setResult($failed ? 'failed' : 'success');
        }

        // Flush immediately if the job is processed in a long-running process.
        /*
         * We do not have to flush if the application is using the sync driver.
         * In that case the package consider the job as a segment.
         * This can cause the "Undefined property: Palzin\Laravel\Palzin::$transaction" error.
         *
         */
        if ($this->app->runningInConsole() && config('queue.default') !== 'sync') {
            Palzin::flush();
        }

    }

    /**
     * Get the job ID.
     *
     * @param Job $job
     * @return string|int
     */
    public static function getJobId(Job $job)
    {
        if ($jobId = $job->getJobId()) {
            return $jobId;
        }

        return sha1($job->getRawBody());
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
     * Determine if the given job needs to be monitored.
     *
     * @param string $job
     * @return bool
     */
    protected function shouldBeMonitored(string $job): bool
    {
        return Filters::isApprovedJobClass($job, config('palzin-apm.ignore_jobs')) && Palzin::isRecording();
    }
}
