<?php

namespace Discuz\Api\Serializer;

use Tobscure\JsonApi\AbstractSerializer as BaseAbstractSerializer;

abstract class AbstractSerializer extends BaseAbstractSerializer
{
    /**
     * {@inheritdoc}
     */
    public function getAttributes($model, array $fields = null)
    {
        if (! is_object($model) && ! is_array($model)) {
            return [];
        }

        $attributes = $this->getDefaultAttributes($model);

        return $attributes;
    }

    abstract public function getDefaultAttributes($model);
}
