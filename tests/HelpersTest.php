<?php


namespace Palzin\Laravel\Tests;


use Palzin\Palzin;

class HelpersTest extends BasicTestCase
{
    public function testGenerateInstance()
    {
        $this->assertInstanceOf(Palzin::class, \palzin());
    }
}
