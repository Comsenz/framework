<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
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
