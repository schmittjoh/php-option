<?php

namespace PHPOption\Tests;

class NoneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testGet()
    {
        $none = \PHPOption\None::create();
        $none->get();
    }

    public function testGetOrElse()
    {
        $none = \PHPOption\None::create();
        $this->assertEquals('foo', $none->getOrElse('foo'));
    }

    public function testGetOrCall()
    {
        $none = \PHPOption\None::create();
        $this->assertEquals('foo', $none->getOrCall(function() { return 'foo'; }));
    }

    public function testIsEmpty()
    {
        $none = \PHPOption\None::create();
        $this->assertTrue($none->isEmpty());
    }
}