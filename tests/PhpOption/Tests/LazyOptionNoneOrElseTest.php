<?php

namespace PhpOption\Tests;

use PhpOption\LazyOption;
use PhpOption\None;

/**
 * @covers LazyOption::orElse
 */
class LazyOptionNoneOrElseTest extends NoneOrElseTest
{
    protected function none()
    {
        return new LazyOption(function() {
           return None::create();
        });
    }
}
