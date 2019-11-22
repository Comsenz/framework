<?php

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Api\Controller;

use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\SerializerInterface;

abstract class AbstractListController extends AbstractSerializeController
{
    /**
     * {@inheritdoc}
     */
    protected function createElement($data, SerializerInterface $serializer)
    {
        return new Collection($data, $serializer);
    }
}
