<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: AbstractUploadTool.phppository.php 28830 2019-10-08 16:39 chenkeke $
 */

namespace Discuz\Foundation;

use Discuz\Contracts\Tool\UploadTool;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class AbstractUploadTool implements UploadTool
{
    /**
     * @var type
     */
    protected $type = 'common';

    /**
     * @model model
     */
    protected $single;

    /**
     * @model model
     */
    protected $multiple;

    /**
     * {@inheritdoc}
     */
    public function setSingleData(Model $single)
    {
        $this->single = $single;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSingleData(): Model
    {
        return $this->single;
    }

    /**
     * {@inheritdoc}
     */
    public function setMultipleData(Model $multiple)
    {
        $this->multiple = $multiple;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getMultipleData(): Model
    {
        return $this->multiple;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    };

    /**
     * {@inheritdoc}
     */
    public function getUploadPath(string $path = '')
    {
        return ($path?$path:$this->type).'/';
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadName(string $extension = '')
    {
        return Str::random().($extension?'.'.$extension:'');
    }
}