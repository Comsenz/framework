<?php


namespace Discuz\Api\Controller;

use Tobscure\JsonApi\Collection;

abstract class AbstractListController extends AbstractSerializeController
{

    public function createElement($data, $serializer)
    {
        return new Collection($data, $serializer);
    }
}
