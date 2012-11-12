<?php

namespace PhpOption\Tests;

class FactoryMethodsTest extends \PHPUnit_Framework_TestCase
{
    public function testNullFactoryMethod()
    {
        $this->assertInstanceOf('PhpOption\None', \PhpOption\Option::notNull(null));
        $this->assertInstanceOf('PhpOption\Some', \PhpOption\Option::notNull('value'));
    }

    public function testFalseFactoryMethod()
    {
        $this->assertInstanceOf('PhpOption\None', \PhpOption\Option::notFalse(false));
        $this->assertInstanceOf('PhpOption\Some', \PhpOption\Option::notFalse('value'));
        $this->assertInstanceOf('PhpOption\Some', \PhpOption\Option::notFalse(null));
    }

    public function testCountFactoryMethod()
    {
        $this->assertInstanceOf('PhpOption\None', \PhpOption\Option::notZeroLength(array()));
        $this->assertInstanceOf('PhpOption\None', \PhpOption\Option::notZeroLength(new \ArrayObject(array())));
        $this->assertInstanceOf('PhpOption\Some', \PhpOption\Option::notZeroLength('value'));
        $this->assertInstanceOf('PhpOption\Some', \PhpOption\Option::notZeroLength(array('foo')));
        $this->assertInstanceOf('PhpOption\Some', \PhpOption\Option::notZeroLength(0));
        $this->assertInstanceOf('PhpOption\Some', \PhpOption\Option::notZeroLength(-1));
        $this->assertInstanceOf('PhpOption\Some', \PhpOption\Option::notZeroLength(null));
        $this->assertInstanceOf('PhpOption\Some', \PhpOption\Option::notZeroLength(new \ArrayObject(array('foo'))));
    }
}