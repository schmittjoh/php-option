<?php

namespace PhpOption\Tests;

use PhpOption\LazyOption;

class LazyOptionTest extends \PHPUnit_Framework_TestCase
{
    private $subject;

    public function setUp()
    {
        $this->subject = $this
            ->getMockBuilder('Subject')
            ->setMethods(array('execute'))
            ->getMock();
    }

    public function testGetWithArgumentsAndConstructor()
    {
        $some = \PhpOption\LazyOption::create(array($this->subject, 'execute'), array('foo'));

        $this->subject
            ->expects($this->once())
            ->method('execute')
            ->with('foo')
            ->will($this->returnValue(\PhpOption\Some::create('foo')));

        $this->assertEquals('foo', $some->get());
        $this->assertEquals('foo', $some->getOrElse(null));
        $this->assertEquals('foo', $some->getOrCall('does_not_exist'));
        $this->assertEquals('foo', $some->getOrThrow(new \RuntimeException('does_not_exist')));
        $this->assertFalse($some->isEmpty());
    }

    public function testGetWithArgumentsAndCreate()
    {
        $some = new \PhpOption\LazyOption(array($this->subject, 'execute'), array('foo'));

        $this->subject
            ->expects($this->once())
            ->method('execute')
            ->with('foo')
            ->will($this->returnValue(\PhpOption\Some::create('foo')));

        $this->assertEquals('foo', $some->get());
        $this->assertEquals('foo', $some->getOrElse(null));
        $this->assertEquals('foo', $some->getOrCall('does_not_exist'));
        $this->assertEquals('foo', $some->getOrThrow(new \RuntimeException('does_not_exist')));
        $this->assertFalse($some->isEmpty());
    }

    public function testGetWithoutArgumentsAndConstructor()
    {
        $some = new \PhpOption\LazyOption(array($this->subject, 'execute'));

        $this->subject
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(\PhpOption\Some::create('foo')));

        $this->assertEquals('foo', $some->get());
        $this->assertEquals('foo', $some->getOrElse(null));
        $this->assertEquals('foo', $some->getOrCall('does_not_exist'));
        $this->assertEquals('foo', $some->getOrThrow(new \RuntimeException('does_not_exist')));
        $this->assertFalse($some->isEmpty());
    }

    public function testGetWithoutArgumentsAndCreate()
    {
        $option = \PhpOption\LazyOption::create(array($this->subject, 'execute'));

        $this->subject
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(\PhpOption\Some::create('foo')));

        $this->assertTrue($option->isDefined());
        $this->assertFalse($option->isEmpty());
        $this->assertEquals('foo', $option->get());
        $this->assertEquals('foo', $option->getOrElse(null));
        $this->assertEquals('foo', $option->getOrCall('does_not_exist'));
        $this->assertEquals('foo', $option->getOrThrow(new \RuntimeException('does_not_exist')));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage None has no value
     */
    public function testCallbackReturnsNull()
    {
        $option = \PhpOption\LazyOption::create(array($this->subject, 'execute'));

        $this->subject
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(\PhpOption\None::create()));

        $this->assertFalse($option->isDefined());
        $this->assertTrue($option->isEmpty());
        $this->assertEquals('alt', $option->getOrElse('alt'));
        $this->assertEquals('alt', $option->getOrCall(function(){return 'alt';}));

        $option->get();
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Expected instance of \PhpOption\Option
     */
    public function testExceptionIsThrownIfCallbackReturnsNonOption()
    {
        $option = \PhpOption\LazyOption::create(array($this->subject, 'execute'));

        $this->subject
            ->expects($this->once())
            ->method('execute')
            ->will($this->returnValue(null));

        $this->assertFalse($option->isDefined());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid callback given
     */
    public function testInvalidCallbackAndConstructor()
    {
        new \PhpOption\LazyOption('invalidCallback');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Invalid callback given
     */
    public function testInvalidCallbackAndCreate()
    {
        \PhpOption\LazyOption::create('invalidCallback');
    }

    public function testifDefined()
    {
        $called = false;
        $self = $this;
        $this->assertNull(LazyOption::fromValue('foo')->ifDefined(function($v) use (&$called, $self) {
            $called = true;
            $self->assertEquals('foo', $v);
        }));
        $this->assertTrue($called);
    }

    public function testForAll()
    {
        $called = false;
        $self = $this;
        $this->assertInstanceOf('PhpOption\Some', LazyOption::fromValue('foo')->forAll(function($v) use (&$called, $self) {
            $called = true;
            $self->assertEquals('foo', $v);
        }));
        $this->assertTrue($called);
    }

    public function testOrElse()
    {
        $some = \PhpOption\Some::create('foo');
        $lazy = \PhpOption\LazyOption::create(function() use ($some) {return $some;});
        $this->assertSame($some, $lazy->orElse(\PhpOption\None::create()));
        $this->assertSame($some, $lazy->orElse(\PhpOption\Some::create('bar')));
    }
}
