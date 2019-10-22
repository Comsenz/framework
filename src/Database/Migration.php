<?php


namespace Discuz\Database;

use Illuminate\Database\Migrations\Migration as BaseMigration;
use Illuminate\Database\Schema\Builder;

class Migration extends BaseMigration
{

    public function schema() : Builder {
        return app('db')->connection()->getSchemaBuilder();
    }
}
