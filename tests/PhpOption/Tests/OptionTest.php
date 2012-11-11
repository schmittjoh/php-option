<?php

namespace PhpOption\Tests;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    public function testNull()
    {
        $option = \PhpOption\Option::create(null);

        $this->assertInstanceof('PhpOption\None', $option);
        $this->assertSame('foo', $option->getOrElse('foo'));
    }

    public function testValue()
    {
        $option = \PhpOption\Option::create('foo');

        $this->assertInstanceOf('PhpOption\Some', $option);
        $this->assertSame('foo', $option->get());
    }
}