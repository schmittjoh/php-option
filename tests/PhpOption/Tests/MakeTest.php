<?php

namespace PhpOption\Tests;

use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

/**
 * Tests for Option::make() method
 * @covers Option::make
 */
class MakeTest extends \PHPUnit_Framework_TestCase
{
    protected function make($value, $noneValue = null)
    {
        $option = Option::make($value, $noneValue);
        $this->assertInstanceOf('PhpOption\Option', $option);
        return $option;
    }

    public function testMixedValue()
    {
        $option = $this->make(1);
        $this->assertTrue($option->isDefined());
        $this->assertSame(1, $option->get());
        $this->assertFalse($this->make(null)->isDefined());
        $this->assertFalse($this->make(1,1)->isDefined());
    }

    public function testReturnValue()
    {
        $option = $this->make(function() { return 1; });
        $this->assertTrue($option->isDefined());
        $this->assertSame(1, $option->get());
        $this->assertFalse($this->make(function() { return null; })->isDefined());
        $this->assertFalse($this->make(function() { return 1; }, 1)->isDefined());
    }

    public function testOptionReturnsAsSameInstance()
    {
        $option = $this->make(1);
        $this->assertSame($option, $this->make($option));
    }

    public function testOptionReturnedFromClosure()
    {
        $option = $this->make(function() { return Some::create(1); });
        $this->assertTrue($option->isDefined());
        $this->assertSame(1, $option->get());

        $option = $this->make(function() { return None::create(); });
        $this->assertFalse($option->isDefined());
    }
}
