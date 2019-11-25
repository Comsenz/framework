<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Search;

use Discuz\Contracts\Search\Search;
use Discuz\Contracts\Search\SearchBuilder;
use Discuz\Contracts\Search\Searcher;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class MysqlSearcher implements Searcher
{
    /**
     * 容器.
     *
     * @var Container
     */
    protected $container;

    /**
     * 搜索需要的数据来源.
     *
     * @var Collection
     */
    protected $searchSource = null;

    /**
     * 查询构建器.
     *
     * @var SearchBuilder
     */
    protected $searchBuilder = null;

    /**
     * 搜索结果.
     *
     * @var Collection
     */
    protected $searchResults = null;

    /**
     * 创建一个新的查询实例.
     *
     * @param  Container  $container
     * @return void
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * 应用一个数据来源.
     *
     * @param Search $search
     * @return MysqlSearcher
     * @throws SearchBuilderException
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function apply(Search $search)
    {
        $this->searchSource = $search;

        $builder = get_class($search).'Builder';

        if (class_exists($builder)) {
            $this->searchBuilder = $this->container->make($builder);
        } else {
            throw new SearchBuilderException('The search builder is undefined.');
        }

        return $this;
    }

    /**
     * 快速查询结果.
     *
     * @return MysqlSearcher
     */
    public function search()
    {
        $this->conditions();

        $this->withs();

        $this->order();

        $this->limit();

        return $this;
    }

    /**
     * 分发处理条件到[查询构建器].
     *
     * @param array $condition
     * @return MysqlSearcher
     */
    public function conditions(array $condition = [])
    {
        $actor = $this->searchSource->getActor();

        $query = $this->searchSource->getQuery();

        $filter = $this->searchSource->getFilter();

        $condition = array_merge($filter, $condition);

        foreach ($condition as $key => $content) {
            if (method_exists($this->searchBuilder, $key)) {
                $this->searchBuilder->$key($actor, $query, $content);
            }
        }

        return $this;
    }

    /**
     * 分发关联关系到[查询构建器].
     *
     * @param array $withs
     * @return MysqlSearcher
     */
    public function withs(array $withs = [])
    {
        $actor = $this->searchSource->getActor();

        $query = $this->searchSource->getQuery();

        $includes = $this->searchSource->getIncludes();

        foreach ($includes as $include) {
            $include = $this->getWithName($include);

            if (!isset($withs[$include]) && method_exists($this->searchBuilder, $include)) {
                $withs[$include] = function ($query) use ($actor, $include) {
                    $this->searchBuilder->$include($actor, $query);
                };
            } elseif (!in_array($include, $withs)) {
                $withs[] = $include;
            }
        }

        $query->with($withs);

        return $this;
    }

    /**
     * 处理排序.
     *
     * @return MysqlSearcher
     */
    public function order()
    {
        $query = $this->searchSource->getQuery();

        $sort = $this->searchSource->getSort();

        if (is_callable($sort)) {
            $sort($query);
        } else {
            foreach ($sort as $field => $order) {
                if (is_array($order)) {
                    foreach ($order as $value) {
                        $query->orderByRaw(Str::snake($field).' != ?', [$value]);
                    }
                } else {
                    $query->orderBy(Str::snake($field), $order);
                }
            }
        }

        return $this;
    }

    /**
     * 处理分页.
     *
     * @return MysqlSearcher
     */
    public function limit()
    {
        $query = $this->searchSource->getQuery();

        $offset = $this->searchSource->getOffset();

        $limit = $this->searchSource->getLimit();

        if ($offset > 0) {
            $query->skip($offset);
        }

        if ($limit > 0) {
            $query->take($limit);
        }

        return $this;
    }

    /**
     * 返回查询的结果[单条].
     *
     * @param bool $reset 是否重新获取结果
     * @return Model
     */
    public function getSingle($reset = false): Model
    {
        if ($reset || !($this->searchResults instanceof Model)) {
            $this->searchResults = $this->searchSource->getQuery()->firstOrFail();
        }

        return $this->searchResults;
    }

    /**
     * 返回查询的结果[多条].
     *
     * @param bool $reset 是否重新获取结果
     * @return Collection
     */
    public function getMultiple($reset = false): Collection
    {
        if ($reset || !($this->searchResults instanceof Collection)) {
            $this->searchResults = $this->searchSource->getQuery()->get();
        }

        return $this->searchResults;
    }

    /**
     * 获取数据来源的关联关系.
     *
     * @return array
     */
    public function getIncludes()
    {
        if (isset($this->searchSource)) {
            return $this->searchSource->getIncludes();
        }

        return [];
    }

    /**
     * 获取给定关系的方法名.
     *
     * @param string $name
     * @return string
     */
    private function getWithName($name)
    {
        if (stripos($name, '-')) {
            $name = lcfirst(implode('', array_map('ucfirst', explode('-', $name))));
        }

        return $name;
    }
}
