<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Discuz\Foundation\Bus;

class PendingChain
{
    /**
     * The class name of the job being dispatched.
     *
     * @var string
     */
    public $class;

    /**
     * The jobs to be chained.
     *
     * @var array
     */
    public $chain;

    /**
     * Create a new PendingChain instance.
     *
     * @param  string  $class
     * @param  array  $chain
     * @return void
     */
    public function __construct($class, $chain)
    {
        $this->class = $class;
        $this->chain = $chain;
    }

    /**
     * Dispatch the job with the given arguments.
     *
     * @return \Illuminate\Foundation\Bus\PendingDispatch
     */
    public function dispatch()
    {
        return (new PendingDispatch(
            new $this->class(...func_get_args())
        ))->chain($this->chain);
    }
}
