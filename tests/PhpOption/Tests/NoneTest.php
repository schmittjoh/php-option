<?php

namespace PhpOption\Tests;

class NoneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testGet()
    {
        $none = \PhpOption\None::create();
        $none->get();
    }

    public function testGetOrElse()
    {
        $none = \PhpOption\None::create();
        $this->assertEquals('foo', $none->getOrElse('foo'));
    }

    public function testGetOrCall()
    {
        $none = \PhpOption\None::create();
        $this->assertEquals('foo', $none->getOrCall(function() { return 'foo'; }));
    }

    public function testIsEmpty()
    {
        $none = \PhpOption\None::create();
        $this->assertTrue($none->isEmpty());
    }

    public function testOrElse()
    {
        $option = \PhpOption\Some::create('foo');
        $this->assertSame($option, \PhpOption\None::create()->orElse($option));
    }
}