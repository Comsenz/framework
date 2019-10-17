<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: SearchBuilderException.php 28830 2019-10-17 17:39 chenkeke $
 */

namespace Discuz\Search;


use Exception;

class SearchBuilderException extends Exception
{
    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}