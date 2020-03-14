<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Http\Middleware;

use App\Models\User;
use App\Passport\Repositories\AccessTokenRepository;
use Discuz\Auth\Guest;
use Illuminate\Support\Arr;
use League\OAuth2\Server\ResourceServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use League\OAuth2\Server\CryptKey;

class AuthenticateWithHeader implements MiddlewareInterface
{
    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \League\OAuth2\Server\Exception\OAuthServerException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $headerLine = $request->getHeaderLine('authorization');

        // 允许 get 携带 Token
        if (! $headerLine) {
            $headerLine = Arr::get($request->getQueryParams(), 'token');

            if ($headerLine) {
                $request = $request->withHeader('authorization', $headerLine);
            }
        }

        $request = $request->withAttribute('actor', new Guest());

        if ($headerLine) {
            $accessTokenRepository = new AccessTokenRepository();

            $publickey = new CryptKey(storage_path('cert/public.key'), '', false);

            $server = new ResourceServer($accessTokenRepository, $publickey);

            $request = $server->validateAuthenticatedRequest($request);

            // 获取Token位置，根据 Token 解析用户并查询到当前用户
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
