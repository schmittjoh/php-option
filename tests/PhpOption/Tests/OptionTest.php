<?php

namespace PhpOption\Tests;

use ArrayAccess;
use LogicException;
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

        $object = new SomeArrayObject();
        $object['foo'] = 'foo';
        $this->assertEquals(new Some('foo'), Option::fromArraysValue($object, 'foo'));
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
            throw new LogicException('Should never be called.');
        };

        $a = new Some('a');
        $b = new LazyOption($throws);

        $this->assertEquals('a', $a->orElse($b)->get());
    }

    public function testOrElseWithMultipleAlternatives()
    {
        $throws = new LazyOption(function () {
            throw new LogicException('Should never be called.');
        });
        $returns = new LazyOption(function () {
            return new Some('foo');
        });

        $a = None::create();

        $this->assertEquals('foo', $a->orElse($returns)->orElse($throws)->get());
    }

    public function testLift()
    {
        $f = function ($a, $b) {
            return $a + $b;
        };

        $fL = Option::lift($f);

        $a = new Some(1);
        $b = new Some(5);
        $n = None::create();

        $this->assertEquals(6, $fL($a, $b)->get());
        $this->assertEquals(6, $fL($b, $a)->get());
        $this->assertEquals($n, $fL($a, $n));
        $this->assertEquals($n, $fL($n, $a));
        $this->assertEquals($n, $fL($n, $n));
    }

    public function testLiftDegenerate()
    {
        $f = function () {
        };

        $fL1 = Option::lift($f);
        $fL2 = Option::lift($f, false);

        $this->assertEquals(None::create(), $fL1());
        $this->assertEquals(Some::create(null), $fL2());
    }
}

class SomeArrayObject implements ArrayAccess
{
    private $data = [];

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}
