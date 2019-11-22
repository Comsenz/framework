<?php

declare(strict_types=1);

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Search;

use Discuz\Contracts\Search\Searcher;
use Discuz\Foundation\AbstractServiceProvider;

class SearchServiceProvider extends AbstractServiceProvider
{
    public function register()
    {
        $this->app->bind(Searcher::class, function ($app) {
            return new MysqlSearcher($app);
        });
    }
}
