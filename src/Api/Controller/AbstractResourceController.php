<?php


namespace Discuz\Api\Controller;

use Tobscure\JsonApi\Resource;

abstract class AbstractResourceController extends AbstractSerializeController
{

    public function createElement($data, $serializer)
    {
       return new Resource($data, $serializer);
    }
}
