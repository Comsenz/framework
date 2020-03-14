<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Database\Console;

use Discuz\Database\MigrationCreator;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand as IlluminateMigrateMakeCommand;
use Illuminate\Support\Composer;

class MigrateMakeCommand extends IlluminateMigrateMakeCommand
{
    public function __construct(MigrationCreator $creator, Composer $composer)
    {
        parent::__construct($creator, $composer);
    }
}
