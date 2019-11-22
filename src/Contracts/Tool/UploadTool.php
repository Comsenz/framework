<?php

declare(strict_types=1);

/*
 *
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 *
 */

namespace Discuz\Contracts\Tool;

use Psr\Http\Message\UploadedFileInterface;

interface UploadTool
{
    public function upload(UploadedFileInterface $file);

    public function save();

    public function verifyFileType(array $type = []);

    public function verifyFileSize(int $size = 0);

    public function getUploadPath();

    public function getUploadName();

    public function getUploadFullPath();
}
