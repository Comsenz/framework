<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: UploadTool.php 28830 2019-10-09 17:45 chenkeke $
 */

namespace Discuz\Contracts\Tool;

use Illuminate\Database\Eloquent\Model;

interface UploadTool
{
    /**
     * {@inheritdoc}
     */
    public function setSingleData(Model $single);

    /**
     * {@inheritdoc}
     */
    public function getSingleData();

    /**
     * {@inheritdoc}
     */
    public function setMultipleData(Model $multiple);

    /**
     * {@inheritdoc}
     */
    public function getMultipleData();

    /**
     * {@inheritdoc}
     */
    public function getType();

    /**
     * {@inheritdoc}
     */
    public function getUploadPath(string $path = '');

    /**
     * {@inheritdoc}
     */
    public function getUploadName(string $extension = '');
}