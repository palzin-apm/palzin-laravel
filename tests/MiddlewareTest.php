<?php
namespace Palzin\Laravel\Tests;
use Palzin\Laravel\Facades\Palzin;
use Palzin\Laravel\Middleware\WebRequestMonitoring;
use Palzin\Models\Transaction;
class MiddlewareTest extends BasicTestCase
{
    public function testIsRecording()
    {
        $this->assertTrue(Palzin::isRecording());
        $this->assertTrue(Palzin::needTransaction());
        $this->app->router->get('test', function () {})
            ->middleware(WebRequestMonitoring::class);
        $this->get('test');
        $this->assertFalse(Palzin::needTransaction());
        $this->assertInstanceOf(Transaction::class, Palzin::transaction());
    }
    public function testResult()
    {
// test the middleware
        $this->app->router->get('test', function () {})
            ->middleware(WebRequestMonitoring::class);
        $response = $this->get( 'test');
        $this->assertEquals(
            $response->getStatusCode(),
            Palzin::transaction()->result
        );
        $this->assertArrayHasKey('Response', Palzin::transaction()->context);
    }
    public function testContext()
    {
// test the middleware
        $this->app->router->get('test', function () {})
            ->middleware(WebRequestMonitoring::class);
        $this->get( 'test');
        $this->assertArrayHasKey('Request Body', Palzin::transaction()->context);
        $this->assertArrayHasKey('Response', Palzin::transaction()->context);
    }
}