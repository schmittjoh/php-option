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

final class Some extends Option
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public static function create($value)
    {
        return new self($value);
    }

    public function isDefined()
    {
        return true;
    }

    public function isEmpty()
    {
        return false;
    }

    public function get()
    {
        return $this->value;
    }

    public function getOrElse($default)
    {
        return $this->value;
    }

    public function getOrCall($callable)
    {
        return $this->value;
    }

    public function getOrThrow(\Exception $ex)
    {
        return $this->value;
    }

    public function orElse(Option $else)
    {
        return $this;
    }

    public function ifDefined($callable)
    {
        call_user_func($callable, $this->value);
    }

    public function map($callable)
    {
        return new self(call_user_func($callable, $this->value));
    }

    public function flatMap($callable)
    {
        $rs = call_user_func($callable, $this->value);
        if ( ! $rs instanceof Option) {
            throw new \RuntimeException('Callables passed to flatMap() must return an Option. Maybe you should use map() instead?');
        }

        return $rs;
    }

    public function filter($callable)
    {
        if (true === call_user_func($callable, $this->value)) {
            return $this;
        }

        return None::create();
    }

    public function filterNot($callable)
    {
        if (false === call_user_func($callable, $this->value)) {
            return $this;
        }

        return None::create();
    }

    public function select($value)
    {
        if ($this->value === $value) {
            return $this;
        }

        return None::create();
    }

    public function reject($value)
    {
        if ($this->value === $value) {
            return None::create();
        }

        return $this;
    }
}
