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

namespace Discuz\Web\Controller;

use App\Api\Controller\Settings\ForumSettingsController;
use Discuz\Api\Client;
use Discuz\Auth\Guest;
use Discuz\Foundation\Application;
use Discuz\Http\DiscuzResponseFactory;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Arr;
use Illuminate\View\Factory;
use Less_Parser;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AbstractWebController implements RequestHandlerInterface
{
    protected $app;

    protected $view;

    protected $apiDocument;

    public function __construct(Application $app, Factory $view)
    {
        $this->app = $app;
        $this->view = $view;
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Tobscure\JsonApi\Exception\InvalidParameterException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $filename = $this->app->publicPath().'/assets/css/forum.css';
        if (!file_exists($filename)) {
            $less = [];
            $less[] = $this->app->resourcePath() . '/less/common/variables.less';
            $less[] = $this->app->resourcePath() . '/less/common/mixins.less';
            $less[] = $this->app->resourcePath() . '/less/forum.less';

            $css = $this->compile($less);

            file_put_contents($this->app->publicPath() . '/assets/css/forum.css', $css);
        }
        $request = $request->withAttribute('actor', new Guest());
        $forum = $this->getForum($request);
        $this->view->share('forum', Arr::get($forum, 'attributes'));

        $view = $this->render($request, $this->view);
        /** @var UrlGenerator $url */
        $url = $this->app->make(UrlGenerator::class);

        $view->with([
            'head' => implode("\n", [
                '<link rel="stylesheet" href="'.$url->to('/assets/css/forum.css').'">',
            ]),
            'payload' => [
                'resources' => [
                    $forum
                ],
                'apiDocument' => $this->apiDocument
            ]
        ]);




        if ($view instanceof Renderable) {
            $view = $view->render();
        }
        return DiscuzResponseFactory::HtmlResponse($view);
    }

    protected function compile(array $sources): string
    {
        if (! count($sources)) {
            return '';
        }

        ini_set('xdebug.max_nesting_level', 200);

        $parser = new Less_Parser([
            'compress' => true,
            'cache_dir' => $this->app->storagePath().'/less',
            'import_dirs' => [
                $this->app->basePath('vendor/fortawesome/font-awesome/less') => ''
            ]
        ]);

        foreach ($sources as $source) {
            $parser->parseFile($source);
        }

        return $parser->getCss();
    }

    abstract public function render(ServerRequestInterface $request, Factory $view);

    protected function getApiForum(ServerRequestInterface $request)
    {
        return $this->app->make(Client::class)->send(ForumSettingsController::class, $request->getAttribute('actor'));
    }

    protected function getForum(ServerRequestInterface $request)
    {
        /** @var ResponseInterface $response */
        $response = $this->getApiForum($request);
        return Arr::get(json_decode($response->getBody(), true), 'data');
    }
}
