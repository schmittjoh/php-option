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

/**
 * Base Option Class.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class Option
{
    /**
     * Creates an option given a return value.
     * 
     * This is intended for consuming existing APIs and allows you to easily 
     * convert them to an option. By default, we treat ``null`` as the None case,
     * and everything else as Some.
     * 
     * @param mixed $value The actual return value.
     * @param mixed $noneValue The value which should be considered "None"; null
     *                         by default.
     * 
     * @return Option
     */
    public static function fromValue($value, $noneValue = null)
    {
        if ($value === $noneValue) {
            return None::create();
        }
        
        return new Some($value);
    }
    
    /**
     * Creates a lazy-option with the given callback. 
     * 
     * This is also a helper constructor for lazy-consuming existing APIs where
     * the return value is not yet an option. By default, we treat ``null`` as
     * None case, and everything else as Some.
     * 
     * @param callable $callback The callback to evaluate.
     * @param array $arguments 
     * @param mixed $noneValue The value which should be considered "None"; null
     *                         by default.
     * 
     * @return Option
     */
    public static function fromReturn($callback, array $arguments = array(), $noneValue = null)
    {
        return new LazyOption(function() use ($callback, $arguments, $noneValue) {
            $return = call_user_func_array($callback, $arguments);
            
            if ($return === $noneValue) {
                return None::create();
            }
            
            return new Some($return);
        });
    }
    
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
    abstract public function isEmpty();

    /**
     * Returns true if a value is available, false otherwise.
     *
     * @return boolean
     */
    abstract public function isDefined();

    /**
     * Returns this option if non-empty, or the passed option otherwise.
     * 
     * This can be used to try multiple alternatives, and is especially useful
     * with lazy evaluating options:
     * 
     * ```php
     *     $repo->findSomething()
     *         ->orElse(new LazyOption(array($repo, 'findSomethingElse')))
     *         ->orElse(new LazyOption(array($repo, 'createSomething')));
     * ```
     * 
     * @param Option $else
     * 
     * @return Option
     */
    abstract public function orElse(Option $else);
}
