<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Api\Events;

use App\Models\User;
use DateTime;
use Discuz\Api\Serializer\AbstractSerializer;

/**
 * Prepare API attributes.
 *
 * This event is fired when a serializer is constructing an array of resource
 * attributes for API output.
 */
class Serializing
{
    /**
     * The class doing the serializing.
     *
     * @var AbstractSerializer
     */
    public $serializer;

    /**
     * The model being serialized.
     *
     * @var object
     */
    public $model;

    /**
     * The serialized attributes of the resource.
     *
     * @var array
     */
    public $attributes;

    /**
     * @var User
     */
    public $actor;

    /**
     * @param AbstractSerializer $serializer the class doing the serializing
     * @param array|object       $model      the model being serialized
     * @param array              $attributes the serialized attributes of the resource
     */
    public function __construct(AbstractSerializer $serializer, $model, array &$attributes)
    {
        $this->serializer = $serializer;
        $this->model = $model;
        $this->attributes = &$attributes;
        $this->actor = $serializer->getActor();
    }

    /**
     * @param string $serializer
     *
     * @return bool
     */
    public function isSerializer($serializer)
    {
        return $this->serializer instanceof $serializer;
    }

    /**
     * @return null|string
     */
    public function formatDate(DateTime $date = null)
    {
        if ($date) {
            return $date->format(DateTime::RFC3339);
        }
    }
}
