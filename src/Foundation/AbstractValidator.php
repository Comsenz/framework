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

namespace Discuz\Foundation;

use Illuminate\Support\Arr;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;

abstract class AbstractValidator
{
    protected $validator;

    protected $data;

    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param array $attributes
     * @throws ValidationException
     */
    public function valid(array $attributes)
    {
        $attributes = $this->existsValue($attributes, $this->haveToFields());

        $this->data = $attributes;

        $validator = $this->make($attributes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function make(array $attributes)
    {
        $rules = Arr::only($this->getRules(), array_keys($attributes));

        return $this->validator->make($attributes, $rules, $this->getMessages());
    }

    /**
     * 循环必传值(当required为空时不循环)
     *
     * @param $attributes
     * @param $required
     * @return mixed
     */
    public function existsValue($attributes, $required)
    {
        collect($required)->map(function ($item) use (&$attributes) {
            if (!array_key_exists($item, $attributes)) {
                $attributes[$item] = '';
            }
        });

        return $attributes;
    }

    /**
     * @return array
     */
    abstract protected function getRules();

    /**
     * @return array
     */
    protected function getMessages()
    {
        return [];
    }

    /**
     * 必填字段值
     *
     * @return array
     */
    protected function haveToFields()
    {
        return [];
    }
}
