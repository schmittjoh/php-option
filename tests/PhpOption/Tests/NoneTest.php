<?php

namespace PhpOption\Tests;

use PhpOption\None;
use PhpOption\Some;
use PHPUnit\Framework\TestCase;

class NoneTest extends TestCase
{
    public function testGet(): void
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException('RuntimeException');
        } else {
            $this->setExpectedException('RuntimeException');
        }

        $none = None::create();
        $none->get();
    }

    public function testGetOrElse(): void
    {
        $none = None::create();
        self::assertSame('foo', $none->getOrElse('foo'));
    }

    public function testGetOrCall(): void
    {
        $none = None::create();
        self::assertSame('foo', $none->getOrCall(function () {
            return 'foo';
        }));
    }

    public function testGetOrThrow(): void
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException('RuntimeException');
            $this->expectExceptionMessage('Not Found!');
        } else {
            $this->setExpectedException('RuntimeException', 'Not Found!');
        }

        None::create()->getOrThrow(new \RuntimeException('Not Found!'));
    }

    public function testIsEmpty(): void
    {
        $none = None::create();
        self::assertTrue($none->isEmpty());
    }

    public function testOrElse(): void
    {
        $option = Some::create('foo');
        self::assertSame($option, None::create()->orElse($option));
    }

    public function testifDefined(): void
    {
        $none = None::create();

        self::assertNull($none->ifDefined(function () {
            throw new \LogicException('Should never be called.');
        }));
    }

    public function testForAll(): void
    {
        $none = None::create();

        self::assertSame($none, $none->forAll(function () {
            throw new \LogicException('Should never be called.');
        }));
    }

    public function testMap(): void
    {
        $none = None::create();

        self::assertSame($none, $none->map(function () {
            throw new \LogicException('Should not be called.');
        }));
    }

    public function testFlatMap(): void
    {
        $none = None::create();

        self::assertSame($none, $none->flatMap(function () {
            throw new \LogicException('Should not be called.');
        }));
    }

    public function testFilter(): void
    {
        $none = None::create();

        self::assertSame($none, $none->filter(function () {
            throw new \LogicException('Should not be called.');
        }));
    }

    public function testFilterNot(): void
    {
        $none = None::create();

        self::assertSame($none, $none->filterNot(function () {
            throw new \LogicException('Should not be called.');
        }));
    }

    public function testSelect(): void
    {
        $none = None::create();

        self::assertSame($none, $none->select(null));
    }

    public function testReject(): void
    {
        $none = None::create();

        self::assertSame($none, $none->reject(null));
    }

    public function testForeach(): void
    {
        $none = None::create();

        $called = 0;
        foreach ($none as $value) {
            $called++;
        }

        self::assertSame(0, $called);
    }

    public function testFoldLeftRight(): void
    {
        $none = None::create();

        self::assertSame(1, $none->foldLeft(1, function () {
            $this->fail();
        }));

        self::assertSame(1, $none->foldRight(1, function () {
            $this->fail();
        }));
    }
}
