<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Http\Middleware;

use Discuz\Auth\Exception\PermissionDeniedException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CheckUserStatus implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws PermissionDeniedException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $actor = $request->getAttribute('actor');

        // 被禁用的用户
        if ($actor->status == 1) {
            throw new PermissionDeniedException('ban_user');
        }
        // 审核中的用户
        if ($actor->status == 2) {
            throw new PermissionDeniedException('register_validate');
        }
        // 审核拒绝
        if ($actor->status == 3) {
            throw new PermissionDeniedException('validate_reject');
        }
        // 审核忽略
        if ($actor->status == 4) {
            throw new PermissionDeniedException('validate_ignore');
        }

        return $handler->handle($request);
    }
}
