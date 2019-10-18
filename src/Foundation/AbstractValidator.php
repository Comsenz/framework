<?php

namespace Discuz\Foundation;

use Illuminate\Support\Arr;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;

abstract class AbstractValidator
{

    protected $validator;

    public function __construct(Factory $validator)
    {
        $this->validator = $validator;
    }


    /**
     * @param array $attributes
     * @throws ValidationException
     */
    public function valid(array $attributes) {
        $validator = $this->make($attributes);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    public function make(array $attributes)
    {
        $rules = Arr::only($this->getRules(), array_keys($attributes));

        $validator = $this->validator->make($attributes, $rules, $this->getMessages());

        return $validator;
    }

    /**
     * @return array
     */
    abstract protected function getRules();

    /**
     * @return array
     */
    protected function getMessages() {
        return [];
    }

}
