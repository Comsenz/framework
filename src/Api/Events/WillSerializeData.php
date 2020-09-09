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
use Discuz\Api\Controller\AbstractSerializeController;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class WillSerializeData
{
    /**
     * @var AbstractSerializeController
     */
    public $controller;

    /**
     * @var mixed
     */
    public $data;

    /**
     * @var ServerRequestInterface
     */
    public $request;

    /**
     * @var Document
     */
    public $document;

    /**
     * @var User
     */
    public $actor;

    /**
     * @param AbstractSerializeController $controller
     * @param mixed $data
     * @param ServerRequestInterface $request
     * @param Document $document
     */
    public function __construct(
        AbstractSerializeController $controller,
        &$data,
        ServerRequestInterface $request,
        Document $document
    ) {
        $this->controller = $controller;
        $this->data = &$data;
        $this->request = $request;
        $this->document = $document;
        $this->actor = $request->getAttribute('actor');
    }

    /**
     * @param string $controller
     * @return bool
     */
    public function isController($controller)
    {
        return $this->controller instanceof $controller;
    }
}
