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
     * Returns the value if available, or throws the passed exception.
     *
     * @param \Exception $ex
     *
     * @return mixed
     */
    abstract public function getOrThrow(\Exception $ex);

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

    /**
     * Applies the callable to the value of the option if it is non-empty,
     * and returns the return value of the callable wrapped in Some().
     *
     * If the option is empty, then the callable is not applied.
     *
     * ```php
     *     (new Some("foo"))->map('strtoupper')->get(); // "FOO"
     * ```
     *
     * @param callable $callable
     *
     * @return Option
     */
    abstract public function map($callable);

    /**
     * Applies the callable to the value of the option if it is non-empty, and
     * returns the return value of the callable directly.
     *
     * In contrast to ``map``, the return value of the callable is expected to
     * be an Option itself; it is not automatically wrapped in Some().
     *
     * @param callable $callable must return an Option
     *
     * @return Option
     */
    abstract public function flatMap($callable);

    /**
     * If the option is empty, it is returned immediately without applying the callable.
     *
     * If the option is non-empty, the callable is applied, and if it returns true,
     * the option itself is returned; otherwise, None is returned.
     *
     * @param callable $callable
     *
     * @return Option
     */
    abstract public function filter($callable);

    /**
     * If the option is empty, it is returned immediately without applying the callable.
     *
     * If the option is non-empty, the callable is applied, and if it returns false,
     * the option itself is returned; otherwise, None is returned.
     *
     * @param callable $callable
     *
     * @return Option
     */
    abstract public function filterNot($callable);

    /**
     * If the option is empty, it is returned immediately.
     *
     * If the option is non-empty, and its value does not equal the passed value
     * (via a shallow comparison ===), then None is returned. Otherwise, the
     * Option is returned.
     *
     * In other words, this will filter all but the passed value.
     *
     * @param mixed $value
     *
     * @return Option
     */
    abstract public function select($value);

    /**
     * If the option is empty, it is returned immediately.
     *
     * If the option is non-empty, and its value does equal the passed value (via
     * a shallow comparison ===), then None is returned; otherwise, the Option is
     * returned.
     *
     * In other words, this will let all values through expect the passed value.
     * 
     * @param mixed $value
     *
     * @return Option
     */
    abstract public function reject($value);
}
