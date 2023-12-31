<?php


namespace Palzin\Laravel\Tests;


use Illuminate\Http\Request;
use Palzin\Laravel\Facades\Palzin;
use Palzin\Laravel\Filters;
use Palzin\Laravel\Middleware\WebRequestMonitoring;
use Palzin\Laravel\Tests\Jobs\JobTest;

class FilterClassTest extends BasicTestCase
{
    public function testRequestApproved()
    {
        $this->app->router->get('test', function (Request $request) {
            $this->assertTrue(Filters::isApprovedRequest(config('palzin-apm.ignore_url'), $request));
        })->middleware(WebRequestMonitoring::class);

        $this->call('GET', 'test');
    }

    public function testRequestNotApproved()
    {
        $this->app->router->get('nova', function (Request $request) {
            $this->assertFalse(Filters::isApprovedRequest(config('palzin-apm.ignore_url'), $request));
        })->middleware(WebRequestMonitoring::class);

        $this->call('GET', 'nova');
    }

    public function testJobNotApproved()
    {
        $notAllowed = [JobTest::class];

        $this->assertFalse(Filters::isApprovedJobClass(JobTest::class, $notAllowed));

        $this->assertTrue(Filters::isApprovedJobClass(JobTest::class, config('palzin-apm.ignore_jobs')));

        config()->set('palzin-apm.ignore_jobs', $notAllowed);

        $this->assertFalse(Filters::isApprovedJobClass(JobTest::class, config('palzin-apm.ignore_jobs')));
    }
}
