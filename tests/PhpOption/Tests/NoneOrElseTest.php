<?php

namespace PhpOption\Tests;

use PhpOption\None;
use PhpOption\Some;

/**
 * Test cases for None::orElse
 *
 * @covers None::orElse
 */
class NoneOrElseTest extends \PHPUnit_Framework_TestCase
{
    protected function none()
    {
        return None::create();
    }

    public function testNonePassed()
    {
        $this->assertFalse(
            $this->none()->orElse($this->none())->isDefined()
        );
    }

    public function testSomePassed()
    {
        $option = $this->none()->orElse(Some::create(true));
        $this->assertTrue(
            $option->isDefined()
        );
        $this->assertSame(true, $option->get());
    }

    public function testClosurePassed_ReturnsMixed()
    {
        $option = $this->none()->orElse(function() { return true; });
        $this->assertTrue(
            $option->isDefined()
        );
        $this->assertSame(true, $option->get());
        $this->assertFalse(
            $this->none()->orElse(function() { return null; })->isDefined()
        );
    }

    public function testClosurePassed_ReturnsOption()
    {
        $option = $this->none()->orElse(function() { return Some::create(true); });
        $this->assertTrue($option->isDefined());
        $this->assertSame(true, $option->get());
        $this->assertFalse(
            $this->none()->orElse(function() { return None::create(); })->isDefined()
        );
    }

    public function testClosurePassed_ReturnsClosure()
    {
        $option = $this->none()->orElse(function() { return function() {}; });
        $this->assertTrue($option->isDefined());
        $this->assertInstanceOf('Closure', $option->get());
    }

    public function testMixedPassed()
    {
        $option = $this->none()->orElse(true);
        $this->assertTrue(
            $option->isDefined()
        );
        $this->assertSame(true, $option->get());
        $this->assertFalse(
            $this->none()->orElse(null)->isDefined()
        );
    }
}
