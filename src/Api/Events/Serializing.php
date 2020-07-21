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
     * @param AbstractSerializer $serializer The class doing the serializing.
     * @param object|array $model The model being serialized.
     * @param array $attributes The serialized attributes of the resource.
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
     * @return bool
     */
    public function isSerializer($serializer)
    {
        return $this->serializer instanceof $serializer;
    }

    /**
     * @param DateTime|null $date
     * @return string|null
     */
    public function formatDate(DateTime $date = null)
    {
        if ($date) {
            return $date->format(DateTime::RFC3339);
        }
    }
}
