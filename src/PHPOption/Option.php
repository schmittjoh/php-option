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

namespace PHPOption;

/**
 * Base Option Class.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class Option
{
    /**
     * Returns the value if available, or throws an exception otherwise.
     *
     * @throws \RuntimeException if value is not available
     *
     * @return mixed
     */
    abstract public function get();

    /**
     * Returns the value if available, or the default value if not.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    abstract public function getOrElse($default);

    /**
     * Returns the value if available, or the results of the callable.
     *
     * This is preferable over ``getOrElse`` if the computation of the default
     * value is expensive.
     *
     * @param callable $callable
     *
     * @return mixed
     */
    abstract public function getOrCall($callable);

    /**
     * Returns true if no value is available, false otherwise.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return false;
    }

    /**
     * Returns true if a value is available, false otherwise.
     *
     * @return boolean
     */
    public function isDefined()
    {
        return false;
    }
}