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
    public function testfromValueWithDefaultNoneValue(): void
    {
        self::assertInstanceOf(None::class, Option::fromValue(null));
        self::assertInstanceOf(Some::class, Option::fromValue('value'));
    }

    public function testFromValueWithFalseNoneValue(): void
    {
        self::assertInstanceOf(None::class, Option::fromValue(false, false));
        self::assertInstanceOf(Some::class, Option::fromValue('value', false));
        self::assertInstanceOf(Some::class, Option::fromValue(null, false));
    }

    public function testFromArraysValue(): void
    {
        self::assertEquals(None::create(), Option::fromArraysValue('foo', 'bar'));
        self::assertEquals(None::create(), Option::fromArraysValue(null, 'bar'));
        self::assertEquals(None::create(), Option::fromArraysValue(['foo' => 'bar'], 'baz'));
        self::assertEquals(None::create(), Option::fromArraysValue(['foo' => null], 'foo'));
        self::assertEquals(None::create(), Option::fromArraysValue(['foo' => 'bar'], null));
        self::assertEquals(new Some('foo'), Option::fromArraysValue(['foo' => 'foo'], 'foo'));
        self::assertEquals(new Some('foo'), Option::fromArraysValue([13 => 'foo'], 13));

        $object = new SomeArrayObject();
        $object['foo'] = 'foo';
        self::assertEquals(new Some('foo'), Option::fromArraysValue($object, 'foo'));

        $object = new SomeArrayObject();
        $object[13] = 'foo';
        self::assertEquals(new Some('foo'), Option::fromArraysValue($object, 13));
    }

    public function testFromReturn(): void
    {
        $null = function () {
        };
        $false = function () {
            return false;
        };
        $some = function () {
            return 'foo';
        };

        self::assertTrue(Option::fromReturn($null)->isEmpty());
        self::assertFalse(Option::fromReturn($false)->isEmpty());
        self::assertTrue(Option::fromReturn($false, [], false)->isEmpty());
        self::assertTrue(Option::fromReturn($some)->isDefined());
        self::assertFalse(Option::fromReturn($some, [], 'foo')->isDefined());
    }

    public function testOrElse(): void
    {
        $a = new Some('a');
        $b = new Some('b');

        self::assertSame('a', $a->orElse($b)->get());
    }

    public function testOrElseWithNoneAsFirst(): void
    {
        $a = None::create();
        $b = new Some('b');

        self::assertSame('b', $a->orElse($b)->get());
    }

    public function testOrElseWithLazyOptions(): void
    {
        $throws = function () {
            throw new LogicException('Should never be called.');
        };

        $a = new Some('a');
        $b = new LazyOption($throws);

        self::assertSame('a', $a->orElse($b)->get());
    }

    public function testOrElseWithMultipleAlternatives(): void
    {
        $throws = new LazyOption(function () {
            throw new LogicException('Should never be called.');
        });
        $returns = new LazyOption(function () {
            return new Some('foo');
        });

        $a = None::create();

        self::assertSame('foo', $a->orElse($returns)->orElse($throws)->get());
    }

    public function testLift(): void
    {
        $f = function ($a, $b) {
            return $a + $b;
        };

        $fL = Option::lift($f);

        $a = new Some(1);
        $b = new Some(5);
        $n = None::create();

        self::assertSame(6, $fL($a, $b)->get());
        self::assertSame(6, $fL($b, $a)->get());
        self::assertSame($n, $fL($a, $n));
        self::assertSame($n, $fL($n, $a));
        self::assertSame($n, $fL($n, $n));
    }

    public function testLiftDegenerate(): void
    {
        $f = function () {
        };

        $fL1 = Option::lift($f);
        $fL2 = Option::lift($f, false);

        self::assertEquals(None::create(), $fL1());
        self::assertEquals(Some::create(null), $fL2());
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
