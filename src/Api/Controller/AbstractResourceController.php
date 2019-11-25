<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Api\Controller;

use Tobscure\JsonApi\Resource;
use Tobscure\JsonApi\SerializerInterface;

abstract class AbstractResourceController extends AbstractSerializeController
{
    /**
     * {@inheritdoc}
     */
    protected function createElement($data, SerializerInterface $serializer)
    {
        return new Resource($data, $serializer);
    }
}
