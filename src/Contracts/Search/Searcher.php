<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: MysqlSearcherrcher.php 28830 2019-10-17 17:16 chenkeke $
 */

namespace Discuz\Contracts\Search;


interface Searcher
{
    public function apply(Search $search);

    public function search();

    public function conditions(array $condition = []);

    public function getSingle($reset = false);

    public function getMultiple($reset = false);

    public function getIncludes();
}