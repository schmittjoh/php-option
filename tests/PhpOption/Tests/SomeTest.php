<?php

namespace PhpOption\Tests;

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
}
