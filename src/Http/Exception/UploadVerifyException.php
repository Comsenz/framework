<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: UploadVerifyException.php 28830 2019-10-18 17:56 chenkeke $
 */

namespace Discuz\Http\Exception;


use Exception;

class UploadVerifyException extends Exception
{
    public function __construct($message = '', $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

    }
}