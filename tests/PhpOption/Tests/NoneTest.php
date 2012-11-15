<?php

namespace PhpOption\Tests;

use PhpOption\None;

class NoneTest extends \PHPUnit_Framework_TestCase
{
    private $none;

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

    public function testMap()
    {
        $this->assertSame($this->none, $this->none->map(function() {
            throw new \LogicException('Should not be called.');
        }));
    }

    public function testFilter()
    {
        $this->assertSame($this->none, $this->none->filter(function() {
            throw new \LogicException('Should not be called.');
        }));
    }

    public function testFilterNot()
    {
        $this->assertSame($this->none, $this->none->filterNot(function() {
            throw new \LogicException('Should not be called.');
        }));
    }

    protected function setUp()
    {
        $this->none = None::create();
    }
}