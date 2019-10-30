<?php

namespace Discuz\Api\Serializer;

use App\Events\Serializing;
use App\Models\User;
use Closure;
use DateTime;
use Illuminate\Contracts\Container\BindingResolutionException;
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

    /**
     * @param Request $request
     */
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
     * @throws BindingResolutionException
     */
    public function getAttributes($model, array $fields = null)
    {
        if (! is_object($model) && ! is_array($model)) {
            return [];
        }

        $attributes = $this->getDefaultAttributes($model);

        app()->make('events')->dispatch(
            new Serializing($this, $model, $attributes)
        );

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
    public function hasOne($model, $serializer, $relation = null)
    {
        return $this->buildRelationship($model, $serializer, $relation);
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
    public function hasMany($model, $serializer, $relation = null)
    {
        return $this->buildRelationship($model, $serializer, $relation, true);
    }

    /**
     * @param mixed $model
     * @param string|Closure|SerializerInterface $serializer
     * @param string|null $relation
     * @param bool $many
     * @return Relationship
     * @throws BindingResolutionException
     */
    protected function buildRelationship($model, $serializer, $relation = null, $many = false)
    {
        if (is_null($relation)) {
            list(, , $caller) = debug_backtrace(false, 3);

            $relation = $caller['function'];
        }

        if ($model->$relation) {
            $serializer = $this->resolveSerializer($serializer, $model, $model->$relation);

            $type = $many ? Collection::class : Resource::class;

            $element = new $type($model->$relation, $serializer);

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
            $serializer = $this->resolveSerializerClass($serializer);
        }

        if (! ($serializer instanceof SerializerInterface)) {
            throw new InvalidArgumentException('Serializer must be an instance of '
                .SerializerInterface::class);
        }

        return $serializer;
    }

    /**
     * @param string $class
     * @return object
     * @throws BindingResolutionException
     */
    protected function resolveSerializerClass($class)
    {
        $serializer = app()->make($class);

        // $serializer->setRequest($this->request);

        return $serializer;
    }
}
