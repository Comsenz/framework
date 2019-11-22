<?php

declare(strict_types=1);

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Contracts\Search;

interface Search
{
    /**
     * @return model
     */
    public function getQuery();

    /**
     * @return array
     */
    public function getIncludes();

    /**
     * @return array
     */
    public function getFields();

    /**
     * @return mixed
     */
    public function getFilter();

    /**
     * @return int
     */
    public function getOffset();

    /**
     * @return null|int
     */
    public function getLimit();

    /**
     * @return array
     */
    public function getSort();
}
