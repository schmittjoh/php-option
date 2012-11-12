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

final class LazyOption extends Option
{
    private $callback;

    private $arguments;

    private $option;

    public function __construct($callback, array $arguments = array())
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException('Invalid callback given');
        }
        $this->callback = $callback;
        $this->arguments = $arguments;
    }

    public static function create($callback, array $arguments = array())
    {
        return new self($callback, $arguments);
    }

    public function isDefined()
    {
        return $this->option()->isDefined();
    }

    public function isEmpty()
    {
        return $this->option()->isEmpty();
    }

    public function get()
    {
        return $this->option()->get();
    }

    public function getOrElse($default)
    {
        return $this->option()->getOrElse($default);
    }

    public function getOrCall($callable)
    {
        return $this->option()->getOrCall($callable);
    }

    /**
     * @return Option
     */
    private function option()
    {
        if ($this->option === null) {
            $this->option = call_user_func_array($this->callback, $this->arguments);
            if (!$this->option instanceof Option) {
                $this->option = null;
                throw new \RuntimeException('Expected instance of \PhpOption\Option');
            }
        }

        return $this->option;
    }

    public function orElse(Option $else)
    {
        return $this->option()->orElse($else);
    }
}
