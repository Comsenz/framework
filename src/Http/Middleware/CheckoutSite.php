<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Http\Middleware;

use App\Models\Group;
use App\Models\Order;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;
use Discuz\Contracts\Setting\SettingsRepository;
use Discuz\Foundation\Application;
use Illuminate\Support\Carbon;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CheckoutSite implements MiddlewareInterface
{
    use AssertPermissionTrait;

    protected $app;

    protected $settings;

    public function __construct(Application $app, SettingsRepository $settings)
    {
        $this->app = $app;
        $this->settings = $settings;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws PermissionDeniedException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // get settings
        $siteClose = (bool)$this->settings->get('site_close');
        $siteMode = $this->settings->get('site_mode');

        if (in_array($request->getUri()->getPath(), ['/api/login', '/api/oauth/wechat/miniprogram'])) {
            return $handler->handle($request);
        }
        $actor = $request->getAttribute('actor');
        $siteClose && $this->assertAdmin($actor);

        // 处理 付费模式 逻辑， 过期之后 加入待付费组
        if (! $actor->isAdmin() && $siteMode === 'pay' && Carbon::now()->gt($actor->expired_at)) {
            if(!$this->getOrder($actor)) {
                $actor->setRelation('groups', Group::where('id', Group::UNPAID)->get());
            }
        }

        return $handler->handle($request);
    }

    private function getOrder($actor) {
        if($actor->isGuest()) {
            return false;
        }
        return $actor->orders()
            ->where('type', Order::ORDER_TYPE_REGISTER)
            ->where('status', Order::ORDER_STATUS_PAID)
            ->first();
    }
}
