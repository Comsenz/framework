<?php

namespace Discuz\Api\Serializer;

use Closure;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
use InvalidArgumentException;
use Tobscure\JsonApi\AbstractSerializer as BaseAbstractSerializer;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Relationship;
use Tobscure\JsonApi\Resource;
use Tobscure\JsonApi\SerializerInterface;

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

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param object|array $model
     * @return array
     */
    abstract public function getDefaultAttributes($model);

    /**
     * @param DateTime|null $date
     * @return string|null
     */
    protected function formatDate(DateTime $date = null)
    {
        if ($date) {
            return $date->format(DateTime::RFC3339);
        }
    }

    /**
     * Get a relationship builder for a has-one relationship.
     *
     * @param mixed $model
     * @param string|Closure|SerializerInterface $serializer
     * @param string $relation
     * @return Relationship
     * @throws BindingResolutionException
     */
    public function hasOne($model, $serializer, $relation)
    {
        if ($model->$relation) {
            $serializer = $this->resolveSerializer($serializer, $model, $model->$relation);

            $element = new Resource($model->$relation, $serializer);

            return new Relationship($element);
        }
    }

    /**
     * Get a relationship builder for a has-many relationship.
     *
     * @param mixed $model
     * @param string|Closure|SerializerInterface $serializer
     * @param string $relation
     * @return Relationship
     * @throws BindingResolutionException
     */
    public function hasMany($model, $serializer, $relation)
    {
        if ($model->$relation) {
            $serializer = $this->resolveSerializer($serializer, $model, $model->$relation);

            $element = new Collection($model->$relation, $serializer);

            return new Relationship($element);
        }
    }

    /**
     * @param mixed $serializer
     * @param mixed $model
     * @param mixed $data
     * @return SerializerInterface
     * @throws InvalidArgumentException
     * @throws BindingResolutionException
     */
    protected function resolveSerializer($serializer, $model, $data)
    {
        if ($serializer instanceof Closure) {
            $serializer = call_user_func($serializer, $model, $data);
        }

        if (is_string($serializer)) {
            $serializer = app()->make($serializer);
        }

        if (! ($serializer instanceof SerializerInterface)) {
            throw new InvalidArgumentException('Serializer must be an instance of '
                .SerializerInterface::class);
        }

        return $serializer;
    }
}
