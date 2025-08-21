<?php

namespace PhpOption\Tests;

use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use PHPUnit\Framework\TestCase;

class EnsureTest extends TestCase
{
    private static function ensure($value, $noneValue = null): Option
    {
        $option = Option::ensure($value, $noneValue);
        self::assertInstanceOf(Option::class, $option);

        return $option;
    }

    public function testMixedValue(): void
    {
        $option = self::ensure(1);
        self::assertTrue($option->isDefined());
        self::assertSame(1, $option->get());
        self::assertFalse(self::ensure(null)->isDefined());
        self::assertFalse(self::ensure(1, 1)->isDefined());
    }

    public function testReturnValue(): void
    {
        $option = self::ensure(function () {
            return 1;
        });
        self::assertTrue($option->isDefined());
        self::assertSame(1, $option->get());
        self::assertFalse(self::ensure(function () {
        })->isDefined());
        self::assertFalse(self::ensure(function () {
            return 1;
        }, 1)->isDefined());
    }

    public function testOptionReturnsAsSameInstance(): void
    {
        $option = self::ensure(1);
        self::assertSame($option, self::ensure($option));
    }

    public function testOptionReturnedFromClosure(): void
    {
        $option = self::ensure(function () {
            return Some::create(1);
        });
        self::assertTrue($option->isDefined());
        self::assertSame(1, $option->get());

        $option = self::ensure(function () {
            return None::create();
        });
        self::assertFalse($option->isDefined());
    }

    public function testClosureReturnedFromClosure(): void
    {
        $option = self::ensure(function () {
            return function () {
            };
        });
        self::assertTrue($option->isDefined());
        self::assertInstanceOf('Closure', $option->get());
    }
}
