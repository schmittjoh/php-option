<?php

namespace PhpOption\Tests;

use PhpOption\LazyOption;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    public function testfromValueWithDefaultNoneValue()
    {
        $this->assertInstanceOf(None::class, Option::fromValue(null));
        $this->assertInstanceOf(Some::class, Option::fromValue('value'));
    }

    public function testFromValueWithFalseNoneValue()
    {
        $this->assertInstanceOf(None::class, Option::fromValue(false, false));
        $this->assertInstanceOf(Some::class, Option::fromValue('value', false));
        $this->assertInstanceOf(Some::class, Option::fromValue(null, false));
    }

    public function testFromArraysValue()
    {
        $this->assertEquals(None::create(), Option::fromArraysValue('foo', 'bar'));
        $this->assertEquals(None::create(), Option::fromArraysValue(null, 'bar'));
        $this->assertEquals(None::create(), Option::fromArraysValue(['foo' => 'bar'], 'baz'));
        $this->assertEquals(None::create(), Option::fromArraysValue(['foo' => null], 'foo'));
        $this->assertEquals(new Some('foo'), Option::fromArraysValue(['foo' => 'foo'], 'foo'));
    }

    public function testFromReturn()
    {
        $null = function () {
        };
        $false = function () {
            return false;
        };
        $some = function () {
            return 'foo';
        };

        $this->assertTrue(Option::fromReturn($null)->isEmpty());
        $this->assertFalse(Option::fromReturn($false)->isEmpty());
        $this->assertTrue(Option::fromReturn($false, [], false)->isEmpty());
        $this->assertTrue(Option::fromReturn($some)->isDefined());
        $this->assertFalse(Option::fromReturn($some, [], 'foo')->isDefined());
    }

    public function testOrElse()
    {
        $a = new Some('a');
        $b = new Some('b');

        $this->assertEquals('a', $a->orElse($b)->get());
    }

    public function testOrElseWithNoneAsFirst()
    {
        $a = None::create();
        $b = new Some('b');

        $this->assertEquals('b', $a->orElse($b)->get());
    }

    public function testOrElseWithLazyOptions()
    {
        $throws = function () {
            throw new \LogicException('Should never be called.');
        };

        $a = new Some('a');
        $b = new LazyOption($throws);

        $this->assertEquals('a', $a->orElse($b)->get());
    }

    public function testOrElseWithMultipleAlternatives()
    {
        $throws = new LazyOption(function () {
            throw new \LogicException('Should never be called.');
        });
        $returns = new LazyOption(function () {
            return new Some('foo');
        });

        $a = None::create();

        $this->assertEquals('foo', $a->orElse($returns)->orElse($throws)->get());
    }
}
