<?php


namespace Discuz\Http\Middleware;


use App\Models\User;
use Discuz\Auth\Guest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthenticateWithHeader implements MiddlewareInterface
{

    const TOKEN_PREFIX = 'Token ';

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return Response
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
//        $headerLine = $request->getHeaderLine('authorization');

//        dd($headerLine);
//        $parts = explode(';', $headerLine);

//        if (isset($parts[0]) && starts_with($parts[0], self::TOKEN_PREFIX)) {
//            $id = substr($parts[0], strlen(self::TOKEN_PREFIX));
//
//            if ($key = ApiKey::where('key', $id)->first()) {
//                $key->touch();
//
//                $userId = $parts[1] ?? '';
//                $actor = $key->user ?? $this->getUser($userId);
//
//                $request = $request->withAttribute('apiKey', $key);
//                $request = $request->withAttribute('bypassFloodgate', true);
//                $request = $request->withAttribute('bypassCsrfToken', true);
//            } elseif ($token = AccessToken::find($id)) {
//                $token->touch();
//
//                $actor = $token->user;
//            }
//
//            if (isset($actor)) {
//                $request = $request->withAttribute('actor', $actor);
//                $request = $request->withoutAttribute('session');
//            }
//        }

        // toedo 获取Token位置，根据Token解析用户并查询到当前用户

        $actor = $this->getActor();

        $request = $request->withAttribute('actor', $actor);

        return $handler->handle($request);
    }

    private function getActor() {
        return new Guest();
    }
}
