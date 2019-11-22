<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Api\Serializer;

use App\Models\User;
use Closure;
use DateTime;
use Discuz\Api\Events\Serializing;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Tobscure\JsonApi\AbstractSerializer as BaseAbstractSerializer;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Relationship;
use Tobscure\JsonApi\Resource;
use Tobscure\JsonApi\SerializerInterface;

abstract class AbstractSerializer extends BaseAbstractSerializer
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var User
     */
    protected $actor;

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
        $this->actor = $request->getAttribute('actor');
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes($model, array $fields = null)
    {
        if (!\is_object($model) && !\is_array($model)) {
            return [];
        }

        $attributes = $this->getDefaultAttributes($model);

        app()->make('events')->dispatch(
            new Serializing($this, $model, $attributes)
        );

        return $attributes;
    }

    /**
     * Get a relationship builder for a has-one relationship.
     *
     * @param mixed                              $model
     * @param Closure|SerializerInterface|string $serializer
     * @param null|Closure|string                $relation
     *
     * @return Relationship
     */
    public function hasOne($model, $serializer, $relation = null)
    {
        return $this->buildRelationship($model, $serializer, $relation);
    }

    /**
     * Get a relationship builder for a has-many relationship.
     *
     * @param mixed                              $model
     * @param Closure|SerializerInterface|string $serializer
     * @param null|string                        $relation
     *
     * @return Relationship
     */
    public function hasMany($model, $serializer, $relation = null)
    {
        return $this->buildRelationship($model, $serializer, $relation, true);
    }

    /**
     * Get the default set of serialized attributes for a model.
     *
     * @param array|object $model
     *
     * @return array
     */
    abstract protected function getDefaultAttributes($model);

    /**
     * @return null|string
     */
    protected function formatDate(DateTime $date = null)
    {
        if ($date) {
            return $date->format(DateTime::RFC3339);
        }
    }

    /**
     * @param mixed                              $model
     * @param Closure|SerializerInterface|string $serializer
     * @param null|string                        $relation
     * @param bool                               $many
     *
     * @return Relationship
     */
    protected function buildRelationship($model, $serializer, $relation = null, $many = false)
    {
        if (null === $relation) {
            list(, , $caller) = debug_backtrace(false, 3);

            $relation = $caller['function'];
        }

        if ($model->{$relation}) {
            $serializer = $this->resolveSerializer($serializer, $model, $model->{$relation});

            $type = $many ? Collection::class : Resource::class;

            $element = new $type($model->{$relation}, $serializer);

            return new Relationship($element);
        }
    }

    /**
     * @param mixed $serializer
     * @param mixed $model
     * @param mixed $data
     *
     * @throws InvalidArgumentException
     *
     * @return SerializerInterface
     */
    protected function resolveSerializer($serializer, $model, $data)
    {
        if ($serializer instanceof Closure) {
            $serializer = \call_user_func($serializer, $model, $data);
        }

        if (\is_string($serializer)) {
            $serializer = $this->resolveSerializerClass($serializer);
        }

        if (!($serializer instanceof SerializerInterface)) {
            throw new InvalidArgumentException('Serializer must be an instance of '
                . SerializerInterface::class);
        }

        return $serializer;
    }

    /**
     * @param string $class
     *
     * @return object
     */
    protected function resolveSerializerClass($class)
    {
        $serializer = app()->make($class);

        $serializer->setRequest($this->request);

        return $serializer;
    }
}
