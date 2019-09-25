<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      1: AbstractServiceProvider.php 28830 2019-09-24 18:28 chenkeke $
 */

namespace Discuz\Foundation;

use Illuminate\Support\ServiceProvider;

abstract class AbstractServiceProvider extends ServiceProvider
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
    }
}
