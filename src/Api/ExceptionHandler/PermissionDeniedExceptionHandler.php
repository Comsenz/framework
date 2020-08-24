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

namespace Discuz\Api\ExceptionHandler;

use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Contracts\Setting\SettingsRepository;
use Exception;
use Tobscure\JsonApi\Exception\Handler\ExceptionHandlerInterface;
use Tobscure\JsonApi\Exception\Handler\ResponseBag;

class PermissionDeniedExceptionHandler implements ExceptionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function manages(Exception $e)
    {
        return $e instanceof PermissionDeniedException;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Exception $e)
    {
        $status = 401;

        $error = [
            'status' => (string) $status,
        ];

        // 站点是否关闭
        $settings = app()->make(SettingsRepository::class);

        if ($settings->get('site_close')) {
            $error['code'] = 'site_closed';
            $error['detail'][] = $settings->get('site_close_msg')?:'';
        } elseif ($e->getMessage() == 'ban_user') {
            $error['code'] = 'ban_user';
        } elseif ($e->getMessage() == 'register_validate') {
            $error['code'] = 'register_validate';
        } elseif ($e->getMessage() == 'user_deny') {
            $error['code'] = 'user_deny';
        } elseif ($e->getMessage() == 'validate_reject') {
            $error['code'] = 'validate_reject';
        } elseif ($e->getMessage() == 'validate_ignore') {
            $error['code'] = 'validate_ignore';
        } elseif ($e->getMessage() == 'register_close') {
            $error['code'] = 'register_close';
        } else {
            $error['code'] = 'permission_denied';
        }

        return new ResponseBag($status, [$error]);
    }
}
