<?php


namespace Palzin\Laravel\Tests;


class ConfigurationTest extends BasicTestCase
{
    public function testMaxItems()
    {
        $this->assertSame(150, (int) config('palzin-apm.max_items'));
    }

    public function testKey()
    {
        $this->assertEquals('xxx', config('palzin-apm.key'));
    }
}
