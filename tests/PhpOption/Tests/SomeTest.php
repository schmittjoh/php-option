<?php

namespace PhpOption\Tests;

use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use PHPUnit\Framework\TestCase;

class SomeTest extends TestCase
{
    public function testGet(): void
    {
        $some = new Some('foo');
        self::assertSame('foo', $some->get());
        self::assertSame('foo', $some->getOrElse(null));
        self::assertSame('foo', $some->getOrCall('does_not_exist'));
        self::assertSame('foo', $some->getOrThrow(new \RuntimeException('Not found')));
        self::assertFalse($some->isEmpty());
    }

    public function testCreate(): void
    {
        $some = Some::create('foo');
        self::assertSame('foo', $some->get());
        self::assertSame('foo', $some->getOrElse(null));
        self::assertSame('foo', $some->getOrCall('does_not_exist'));
        self::assertSame('foo', $some->getOrThrow(new \RuntimeException('Not found')));
        self::assertFalse($some->isEmpty());
    }

    public function testOrElse(): void
    {
        $some = Some::create('foo');
        self::assertSame($some, $some->orElse(None::create()));
        self::assertSame($some, $some->orElse(Some::create('bar')));
    }

    public function testifDefined(): void
    {
        $called = false;
        $self = $this;
        $some = new Some('foo');
        $some->ifDefined(function ($v) use (&$called, $self) {
            $called = true;
            $self->assertSame('foo', $v);
        });
        self::assertTrue($called);
    }

    public function testForAll(): void
    {
        $called = false;
        $self = $this;
        $some = new Some('foo');
        self::assertSame($some, $some->forAll(function ($v) use (&$called, $self) {
            $called = true;
            $self->assertSame('foo', $v);
        }));
        self::assertTrue($called);
    }

    public function testMap(): void
    {
        $some = new Some('foo');
        self::assertSame('o', $some->map(function ($v) {
            return substr($v, 1, 1);
        })->get());
    }

    public function testFlatMap(): void
    {
        $repo = new Repository(['foo']);

        self::assertSame(['name' => 'foo'], $repo->getLastRegisteredUsername()
                                                        ->flatMap([$repo, 'getUser'])
                                                        ->getOrCall([$repo, 'getDefaultUser']));
    }

    public function testFilter(): void
    {
        $some = new Some('foo');

        self::assertInstanceOf(None::class, $some->filter(function ($v) {
            return 0 === strlen($v);
        }));
        self::assertSame($some, $some->filter(function ($v) {
            return strlen($v) > 0;
        }));
    }

    public function testFilterNot(): void
    {
        $some = new Some('foo');

        self::assertInstanceOf(None::class, $some->filterNot(function ($v) {
            return strlen($v) > 0;
        }));
        self::assertSame($some, $some->filterNot(function ($v) {
            return strlen($v) === 0;
        }));
    }

    public function testSelect(): void
    {
        $some = new Some('foo');

        self::assertSame($some, $some->select('foo'));
        self::assertInstanceOf(None::class, $some->select('bar'));
        self::assertInstanceOf(None::class, $some->select(true));
    }

    public function testReject(): void
    {
        $some = new Some('foo');

        self::assertSame($some, $some->reject(null));
        self::assertSame($some, $some->reject(true));
        self::assertInstanceOf(None::class, $some->reject('foo'));
    }

    public function testFoldLeftRight(): void
    {
        $some = new Some(5);

        $testObj = $this;
        self::assertSame(6, $some->foldLeft(1, function ($a, $b) use ($testObj) {
            $testObj->assertSame(1, $a);
            $testObj->assertSame(5, $b);

            return $a + $b;
        }));

        self::assertSame(6, $some->foldRight(1, function ($a, $b) use ($testObj) {
            $testObj->assertSame(1, $b);
            $testObj->assertSame(5, $a);

            return $a + $b;
        }));
    }

    public function testForeach(): void
    {
        $some = new Some('foo');

        $called = 0;
        $extractedValue = null;
        foreach ($some as $value) {
            $extractedValue = $value;
            $called++;
        }

        self::assertSame('foo', $extractedValue);
        self::assertSame(1, $called);
    }
}

// For the interested reader of these tests, we have gone some great lengths
// to come up with a non-contrived example that might also be used in the
// real-world, and not only for testing purposes :)
class Repository
{
    private $users;

    public function __construct(array $users = [])
    {
        $this->users = $users;
    }

    // A fast ID lookup, probably cached, sometimes we might not need the entire user.
    public function getLastRegisteredUsername(): Option
    {
        if (empty($this->users)) {
            return None::create();
        }

        return new Some(end($this->users));
    }

    // Returns a user object (we will live with an array here).
    public function getUser($name): Option
    {
        if (in_array($name, $this->users, true)) {
            return new Some(['name' => $name]);
        }

        return None::create();
    }

    public function getDefaultUser(): array
    {
        return ['name' => 'muhuhu'];
    }
}
