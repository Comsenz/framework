<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
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
            $error['detail'] = $settings->get('site_close_msg');
        } elseif ($e->getMessage() == 'ban_user') {
            $error['code'] = 'ban_user';
        } elseif ($e->getMessage() == 'register_validate') {
            $error['code'] = 'register_validate';
        } else {
            $error['code'] = 'permission_denied';
        }

        return new ResponseBag($status, [$error]);
    }
}
