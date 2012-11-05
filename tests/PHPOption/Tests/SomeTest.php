<?php

namespace PHPOption\Tests;

class SomeTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $some = new \PHPOption\Some('foo');
        $this->assertEquals('foo', $some->get());
        $this->assertEquals('foo', $some->getOrElse(null));
        $this->assertEquals('foo', $some->getOrCall('does_not_exist'));
        $this->assertFalse($some->isEmpty());
    }
}