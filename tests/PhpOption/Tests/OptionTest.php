<?php

namespace PhpOption\Tests;

use PhpOption\LazyOption;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    public function testFromValueWithDefaultNoneValue()
    {
        $this->assertInstanceOf('PhpOption\None', Option::fromValue(null));
        $this->assertInstanceOf('PhpOption\Some', Option::fromValue('value'));
    }

    public function testFromValueWithFalseNoneValue()
    {
        $this->assertInstanceOf('PhpOption\None', Option::fromValue(false, false));
        $this->assertInstanceOf('PhpOption\Some', Option::fromValue('value', false));
        $this->assertInstanceOf('PhpOption\Some', Option::fromValue(null, false));
    }

    public function testFromArraysValue()
    {
        $this->assertEquals(None::create(), Option::fromArraysValue('foo', 'bar'));
        $this->assertEquals(None::create(), Option::fromArraysValue(null, 'bar'));
        $this->assertEquals(None::create(), Option::fromArraysValue(array('foo' => 'bar'), 'baz'));
        $this->assertEquals(None::create(), Option::fromArraysValue(array('foo' => null), 'foo'));
        $this->assertEquals(new Some('foo'), Option::fromArraysValue(array('foo' => 'foo'), 'foo'));
    }

    public function testFromReturn()
    {
        $null = function() { return null; };
        $false = function() { return false; };
        $some = function() { return 'foo'; };

        $this->assertTrue(Option::fromReturn($null)->isEmpty());
        $this->assertFalse(Option::fromReturn($false)->isEmpty());
        $this->assertTrue(Option::fromReturn($false, array(), false)->isEmpty());
        $this->assertTrue(Option::fromReturn($some)->isDefined());
        $this->assertFalse(Option::fromReturn($some, array(), 'foo')->isDefined());
    }

    public function testOrElse()
    {
        $a = new Some ('a');
        $b = new Some ('b');

        $this->assertEquals('a', $a->orElse($b)->get());
    }

    public function testOrElseWithNoneAsFirst()
    {
        $a = None::create();
        $b = new Some ('b');

        $this->assertEquals('b', $a->orElse($b)->get());
    }

    public function testOrElseWithLazyOptions()
    {
        $throws = function() { throw new \LogicException('Should never be called.'); };

        $a = new Some ('a');
        $b = new LazyOption($throws);

        $this->assertEquals('a', $a->orElse($b)->get());
    }

    public function testOrElseWithMultipleAlternatives()
    {
        $throws = new LazyOption(function() { throw new \LogicException('Should never be called.'); });
        $returns = new LazyOption(function() { return new Some ('foo'); });

        $a = None::create();

        $this->assertEquals('foo', $a->orElse($returns)->orElse($throws)->get());
    }
}
