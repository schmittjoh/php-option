<?php

/*
 * Copyright 2012 Johannes M. Schmitt <schmittjoh@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PhpOption;

use EmptyIterator;

final class None extends Option
{
    private static $instance;

    public static function create()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function get()
    {
        throw new \RuntimeException('None has no value.');
    }

    public function getOrCall($callable)
    {
        return call_user_func($callable);
    }

    public function getOrElse($default)
    {
        return $default;
    }

    public function getOrThrow(\Exception $ex)
    {
        throw $ex;
    }

    public function isEmpty()
    {
        return true;
    }

    public function isDefined()
    {
        return false;
    }

    public function orElse($else)
    {
        return Option::ensure($else);
    }

    /**
     * @deprecated Use forAll() instead.
     */
    public function ifDefined($callable)
    {
        // Just do nothing in that case.
    }

    public function forAll($callable)
    {
        return $this;
    }

    public function map($callable)
    {
        return $this;
    }

    public function flatMap($callable)
    {
        return $this;
    }

    public function filter($callable)
    {
        return $this;
    }

    public function filterNot($callable)
    {
        return $this;
    }

    public function select($value)
    {
        return $this;
    }

    public function reject($value)
    {
        return $this;
    }

    public function getIterator()
    {
        return new EmptyIterator();
    }

    public function foldLeft($initialValue, $callable)
    {
        return $initialValue;
    }

    public function foldRight($initialValue, $callable)
    {
        return $initialValue;
    }

    private function __construct() { }
}
