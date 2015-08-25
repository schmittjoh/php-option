<?php

namespace PhpOption\Tests;

use PhpOption\None;
use PhpOption\Some;

class SomeTest extends \PHPUnit_Framework_TestCase
{
    public function testGet()
    {
        $some = new Some('foo');
        $this->assertEquals('foo', $some->get());
        $this->assertEquals('foo', $some->getOrElse(null));
        $this->assertEquals('foo', $some->getOrCall('does_not_exist'));
        $this->assertEquals('foo', $some->getOrThrow(new \RuntimeException('Not found')));
        $this->assertFalse($some->isEmpty());
    }

    public function testCreate()
    {
        $some = Some::create('foo');
        $this->assertEquals('foo', $some->get());
        $this->assertEquals('foo', $some->getOrElse(null));
        $this->assertEquals('foo', $some->getOrCall('does_not_exist'));
        $this->assertEquals('foo', $some->getOrThrow(new \RuntimeException('Not found')));
        $this->assertFalse($some->isEmpty());
    }

    public function testOrElse()
    {
        $some = Some::create('foo');
        $this->assertSame($some, $some->orElse(None::create()));
        $this->assertSame($some, $some->orElse(None::create('bar')));
    }

    public function testIfDefined()
    {
        $called = false;
        $self = $this;
        $some = new Some('foo');
        $this->assertNull($some->ifDefined(function($v) use (&$called, $self) {
            $called = true;
            $self->assertEquals('foo', $v);
        }));
        $this->assertTrue($called);
    }

    public function testForAll()
    {
        $called = false;
        $self = $this;
        $some = new Some('foo');
        $this->assertSame($some, $some->forAll(function($v) use (&$called, $self) {
            $called = true;
            $self->assertEquals('foo', $v);
        }));
        $this->assertTrue($called);
    }

    public function testMap()
    {
        $some = new Some('foo');
        $this->assertEquals('o', $some->map(function($v) { return substr($v, 1, 1); })->get());
    }

    public function testFlatMap()
    {
        $repo = new Repository(array('foo'));

        $this->assertEquals(array('name' => 'foo'), $repo->getLastRegisteredUsername()
                                                        ->flatMap(array($repo, 'getUser'))
                                                        ->getOrCall(array($repo, 'getDefaultUser')));
    }

    public function testFilter()
    {
        $some = new Some('foo');

        $this->assertInstanceOf('PhpOption\None', $some->filter(function($v) { return 0 === strlen($v); }));
        $this->assertSame($some, $some->filter(function($v) { return strlen($v) > 0; }));
    }

    public function testFilterNot()
    {
        $some = new Some('foo');

        $this->assertInstanceOf('PhpOption\None', $some->filterNot(function($v) { return strlen($v) > 0; }));
        $this->assertSame($some, $some->filterNot(function($v) { return strlen($v) === 0; }));
    }

    public function testSelect()
    {
        $some = new Some('foo');

        $this->assertSame($some, $some->select('foo'));
        $this->assertInstanceOf('PhpOption\None', $some->select('bar'));
        $this->assertInstanceOf('PhpOption\None', $some->select(true));
    }

    public function testReject()
    {
        $some = new Some('foo');

        $this->assertSame($some, $some->reject(null));
        $this->assertSame($some, $some->reject(true));
        $this->assertInstanceOf('PhpOption\None', $some->reject('foo'));
    }

    public function testFoldLeftRight()
    {
        $some = new Some(5);

        $this->assertSame(6, $some->foldLeft(1, function($a, $b) {
            \PHPUnit_Framework_Assert::assertEquals(1, $a);
            \PHPUnit_Framework_Assert::assertEquals(5, $b);

            return $a + $b;
        }));

        $this->assertSame(6, $some->foldRight(1, function($a, $b) {
            $this->assertEquals(1, $b);
            $this->assertEquals(5, $a);

            return $a + $b;
        }));
    }

    public function testForeach()
    {
        $some = new Some('foo');

        $called = 0;
        $extractedValue = null;
        foreach ($some as $value) {
            $extractedValue = $value;
            $called++;
        }

        $this->assertEquals('foo', $extractedValue);
        $this->assertEquals(1, $called);
    }
}

// For the interested reader of these tests, we have gone some great lengths
// to come up with a non-contrived example that might also be used in the
// real-world, and not only for testing purposes :)
class Repository
{
    private $users;

    public function __construct(array $users = array())
    {
        $this->users = $users;
    }

    // A fast ID lookup, probably cached, sometimes we might not need the entire user.
    public function getLastRegisteredUsername()
    {
        if (empty($this->users)) {
            return None::create();
        }

        return new Some(end($this->users));
    }

    // Returns a user object (we will live with an array here).
    public function getUser($name)
    {
        if (in_array($name, $this->users, true)) {
            return new Some(array('name' => $name));
        }

        return None::create();
    }

    public function getDefaultUser()
    {
        return array('name' => 'muhuhu');
    }
}
