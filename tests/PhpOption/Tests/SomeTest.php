<?php

namespace PhpOption\Tests;

use PhpOption\Some;

class SomeTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $some = new \PhpOption\Some('foo');
        $this->assertEquals('foo', $some->get());
        $this->assertEquals('foo', $some->getOrElse(null));
        $this->assertEquals('foo', $some->getOrCall('does_not_exist'));
        $this->assertFalse($some->isEmpty());
    }

    public function testCreate()
    {
        $some = \PhpOption\Some::create('foo');
        $this->assertEquals('foo', $some->get());
        $this->assertEquals('foo', $some->getOrElse(null));
        $this->assertEquals('foo', $some->getOrCall('does_not_exist'));
        $this->assertFalse($some->isEmpty());
    }

    public function testOrElse()
    {
        $some = \PhpOption\Some::create('foo');
        $this->assertSame($some, $some->orElse(\PhpOption\None::create()));
        $this->assertSame($some, $some->orElse(\PhpOption\Some::create('bar')));
    }

    public function testMap()
    {
        $some = new Some('foo');
        $this->assertEquals('FOO', $some->map('strtoupper')->get());
    }

    public function testFilter()
    {
        $some = new Some('foo');

        $this->assertInstanceOf('PhpOption\None', $some->filter(function($v) { return 0 === strlen($v); }));
        $this->assertSame($some, $some->filter(function($v) { return strlen($v) > 0; }));
    }

    public function testFilterNot()
    {
        $some = new Some('foo');

        $this->assertInstanceOf('PhpOption\None', $some->filterNot(function($v) { return strlen($v) > 0; }));
        $this->assertSame($some, $some->filterNot(function($v) { return strlen($v) === 0; }));
    }
}
