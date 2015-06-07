<?php

namespace PhpOption\Tests;

use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

/**
 * Tests for Option::ensure() method
 *
 * @covers Option::ensure
 */
class EnsureTest extends \PHPUnit_Framework_TestCase
{
    protected function ensure($value, $noneValue = null)
    {
        $option = Option::ensure($value, $noneValue);
        $this->assertInstanceOf('PhpOption\Option', $option);
        return $option;
    }

    public function testMixedValue()
    {
        $option = $this->ensure(1);
        $this->assertTrue($option->isDefined());
        $this->assertSame(1, $option->get());
        $this->assertFalse($this->ensure(null)->isDefined());
        $this->assertFalse($this->ensure(1,1)->isDefined());
    }

    public function testReturnValue()
    {
        $option = $this->ensure(function() { return 1; });
        $this->assertTrue($option->isDefined());
        $this->assertSame(1, $option->get());
        $this->assertFalse($this->ensure(function() { return null; })->isDefined());
        $this->assertFalse($this->ensure(function() { return 1; }, 1)->isDefined());
    }

    public function testOptionReturnsAsSameInstance()
    {
        $option = $this->ensure(1);
        $this->assertSame($option, $this->ensure($option));
    }

    public function testOptionReturnedFromClosure()
    {
        $option = $this->ensure(function() { return Some::create(1); });
        $this->assertTrue($option->isDefined());
        $this->assertSame(1, $option->get());

        $option = $this->ensure(function() { return None::create(); });
        $this->assertFalse($option->isDefined());
    }

    public function testClosureReturnedFromClosure()
    {
        $option = $this->ensure(function() { return function() {}; });
        $this->assertTrue($option->isDefined());
        $this->assertInstanceOf('Closure', $option->get());
    }
}
