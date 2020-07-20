<?php

namespace PhpOption\Tests;

use PhpOption\LazyOption;
use PhpOption\None;
use PhpOption\Option;
use PhpOption\Some;
use PHPUnit\Framework\TestCase;

class LazyOptionTest extends TestCase
{
    private $subject;

    /**
     * @before
     */
    public function setUpSubject()
    {
        $this->subject = $this
            ->getMockBuilder('Subject')
            ->setMethods(['execute'])
            ->getMock();
    }

    public function testGetWithArgumentsAndConstructor()
    {
        $some = LazyOption::create([$this->subject, 'execute'], ['foo']);

        $this->subject
            ->expects($this->once())
            ->method('execute')
            ->with('foo')
            ->will($this->returnValue(Some::create('foo')));

        $this->assertEquals('foo', $some->get());
        $this->assertEquals('foo', $some->getOrElse(null));
        $this->assertEquals('foo', $some->getOrCall('does_not_exist'));
        $this->assertEquals('foo', $some->getOrThrow(new \RuntimeException('does_not_exist')));
        $this->assertFalse($some->isEmpty());
    }

    public function testGetWithArgumentsAndCreate()
    {
        $some = new LazyOption([$this->subject, 'execute'], ['foo']);

        $this->subject
            ->expects($this->once())
            ->method('execute')
            ->with('foo')
            ->will($this->returnValue(Some::create('foo')));

        $this->assertEquals('foo', $some->get());
        $this->assertEquals('foo', $some->getOrElse(null));
        $this->assertEquals('foo', $some->getOrCall('does_not_exist'));
        $this->assertEquals('foo', $some->getOrThrow(new \RuntimeException('does_not_exist')));
        $this->assertFalse($some->isEmpty());
    }

    public function testGetWithoutArgumentsAndConstructor()
    {
        $some = new LazyOption([$this->subject, 'execute']);

        $this->subject
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(Some::create('foo')));

        $this->assertEquals('foo', $some->get());
        $this->assertEquals('foo', $some->getOrElse(null));
        $this->assertEquals('foo', $some->getOrCall('does_not_exist'));
        $this->assertEquals('foo', $some->getOrThrow(new \RuntimeException('does_not_exist')));
        $this->assertFalse($some->isEmpty());
    }

    public function testGetWithoutArgumentsAndCreate()
    {
        $option = LazyOption::create([$this->subject, 'execute']);

        $this->subject
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(Some::create('foo')));

        $this->assertTrue($option->isDefined());
        $this->assertFalse($option->isEmpty());
        $this->assertEquals('foo', $option->get());
        $this->assertEquals('foo', $option->getOrElse(null));
        $this->assertEquals('foo', $option->getOrCall('does_not_exist'));
        $this->assertEquals('foo', $option->getOrThrow(new \RuntimeException('does_not_exist')));
    }

    public function testCallbackReturnsNull()
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException('RuntimeException');
            $this->expectExceptionMessage('None has no value');
        } else {
            $this->setExpectedException('RuntimeException', 'None has no value');
        }

        $option = LazyOption::create([$this->subject, 'execute']);

        $this->subject
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(None::create()));

        $this->assertFalse($option->isDefined());
        $this->assertTrue($option->isEmpty());
        $this->assertEquals('alt', $option->getOrElse('alt'));
        $this->assertEquals('alt', $option->getOrCall(function () {
            return 'alt';
        }));

        $option->get();
    }

    public function testExceptionIsThrownIfCallbackReturnsNonOption()
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException('RuntimeException');
            $this->expectExceptionMessage('Expected instance of PhpOption\Option');
        } else {
            $this->setExpectedException('RuntimeException', 'Expected instance of PhpOption\Option');
        }

        $option = LazyOption::create([$this->subject, 'execute']);

        $this->subject
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(null));

        $this->assertFalse($option->isDefined());
    }

    public function testInvalidCallbackAndConstructor()
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException('InvalidArgumentException');
            $this->expectExceptionMessage('Invalid callback given');
        } else {
            $this->setExpectedException('InvalidArgumentException', 'Invalid callback given');
        }

        new LazyOption('invalidCallback');
    }

    public function testInvalidCallbackAndCreate()
    {
        if (method_exists($this, 'expectException')) {
            $this->expectException('InvalidArgumentException');
            $this->expectExceptionMessage('Invalid callback given');
        } else {
            $this->setExpectedException('InvalidArgumentException', 'Invalid callback given');
        }

        LazyOption::create('invalidCallback');
    }

    public function testifDefined()
    {
        $called = false;
        $self = $this;
        LazyOption::fromValue('foo')->ifDefined(function ($v) use (&$called, $self) {
            $called = true;
            $self->assertEquals('foo', $v);
        });
        $this->assertTrue($called);
    }

    public function testForAll()
    {
        $called = false;
        $self = $this;
        $this->assertInstanceOf(Some::class, LazyOption::fromValue('foo')->forAll(function ($v) use (&$called, $self) {
            $called = true;
            $self->assertEquals('foo', $v);
        }));
        $this->assertTrue($called);
    }

    public function testOrElse()
    {
        $some = Some::create('foo');
        $lazy = LazyOption::create(function () use ($some) {
            return $some;
        });
        $this->assertSame($some, $lazy->orElse(None::create()));
        $this->assertSame($some, $lazy->orElse(Some::create('bar')));
    }

    public function testFoldLeftRight()
    {
        $callback = function () {
        };

        $option = $this->getMockForAbstractClass(Option::class);
        $option->expects($this->once())
            ->method('foldLeft')
            ->with(5, $callback)
            ->will($this->returnValue(6));
        $lazyOption = new LazyOption(function () use ($option) {
            return $option;
        });
        $this->assertSame(6, $lazyOption->foldLeft(5, $callback));

        $option->expects($this->once())
            ->method('foldRight')
            ->with(5, $callback)
            ->will($this->returnValue(6));
        $lazyOption = new LazyOption(function () use ($option) {
            return $option;
        });
        $this->assertSame(6, $lazyOption->foldRight(5, $callback));
    }
}
