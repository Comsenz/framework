<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Http\Middleware;

use App\Models\User;
use App\Passport\Repositories\AccessTokenRepository;
use Discuz\Auth\Guest;
use Discuz\Foundation\Application;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticateWithHeader implements MiddlewareInterface
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $headerLine = $request->getHeaderLine('authorization');

        $request = $request->withAttribute('actor', new Guest());

        if ($headerLine) {
            $accessTokenRepository = new AccessTokenRepository();

            $publickey = $this->app->basePath('config/public.key');

            $server = new ResourceServer($accessTokenRepository, $publickey);

            $request = $server->validateAuthenticatedRequest($request);

            // toedo 获取Token位置，根据Token解析用户并查询到当前用户
            $actor = $this->getActor($request);

            if (!is_null($actor) && $actor->exists) {
                $request = $request->withoutAttribute('oauth_access_token_id')->withoutAttribute('oauth_client_id')->withoutAttribute('oauth_user_id')->withoutAttribute('oauth_scopes')->withAttribute('actor', $actor);
            }
        }

        return $handler->handle($request);
    }

    private function getActor(ServerRequestInterface $request)
    {
        return User::find($request->getAttribute('oauth_user_id'));
    }
}
