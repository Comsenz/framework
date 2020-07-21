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

namespace Discuz\Database;

use Illuminate\Database\Migrations\Migration as BaseMigration;
use Illuminate\Database\Schema\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Migration extends BaseMigration
{
    protected $schema;

    public function schema() : Builder
    {
        return $this->boot($this->getSchema());
    }

    /**
     * Get a fluent query builder instance.
     *
     * @param string $table
     * @param string $connection
     * @return \Illuminate\Database\Query\Builder
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function table($table, $connection = null): QueryBuilder
    {
        return app('db')->connection($connection)->table($table);
    }

    public function getSchema()
    {
        return $this->schema ?? $this->schema = app('db')->connection()->getSchemaBuilder();
    }

    /**
     * @inheritDoc  Laravel 默认使用 utf8mb4 编码，它支持在数据库中储存 emojis 。如果你是在版本低于 5.7.7 的 MySQL 或者版本低于 10.2.2 的 MariaDB 上创建索引，那你就需要手动配置数据库迁移的默认字符串长度。在下面调用 $schema::defaultStringLength(191); 方法来配置它：
     * @inheritDoc   当然，你也可以选择开启数据库的 nnodb_large_prefix 选项。至于如何正确开启，请自行查阅数据库文档。
     * @param Builder $schema
     * @return Builder
     */
    protected function boot(Builder $schema)
    {
        return $schema;
    }
}
