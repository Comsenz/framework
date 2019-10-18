<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: UploadTool.php 28830 2019-10-09 17:45 chenkeke $
 */

namespace Discuz\Contracts\Tool;

use Psr\Http\Message\UploadedFileInterface;

interface UploadTool
{
    /**
     * {@inheritdoc}
     */
    public function upload(UploadedFileInterface $file);

    /**
     * {@inheritdoc}
     */
    public function save();

    /**
     * {@inheritdoc}
     */
    public function verifyFileType(array $type = []);

    /**
     * {@inheritdoc}
     */
    public function verifyFileSize(int $size = 0);

    /**
     * {@inheritdoc}
     */
    public function getUploadPath();

    /**
     * {@inheritdoc}
     */
    public function getUploadName();

    /**
     * {@inheritdoc}
     */
    public function getUploadFullPath();
}