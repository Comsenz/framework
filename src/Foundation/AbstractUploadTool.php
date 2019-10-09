<?php
declare(strict_types=1);

/**
 *      Discuz & Tencent Cloud
 *      This is NOT a freeware, use is subject to license terms
 *
 *      Id: AbstractUploadTool.php 28830 2019-10-08 16:39 chenkeke $
 */

namespace Discuz\Foundation;

use Discuz\Contracts\Tool\UploadTool;
use Illuminate\Contracts\Filesystem\Factory as FileFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Psr\Http\Message\UploadedFileInterface;

abstract class AbstractUploadTool implements UploadTool
{
    /**
     * @model UploadedFileInterface
     */
    protected $file;

    /**
     * @var string
     */
    protected $fileName = '';

    /**
     * @var type
     */
    protected $type = 'common';

    /**
     * @var FileFactory
     */
    protected $driver;

    /**
     * @model model
     */
    protected $single;

    /**
     * @model model
     */
    protected $multiple;

    public function __construct(FileFactory $driver)
    {
        $this->driver = $driver;
    }

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
    public function saveFile()
    {
        $extension = pathinfo($this->file->getClientFilename(), PATHINFO_EXTENSION);

        $uploadPath = $this->getUploadPath();

        $uploadName = $this->getUploadName($extension);

        if ($this->driver->put($uploadPath.$uploadName, $this->file, 'public')){
            $this->fileName = '';
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setFile(UploadedFileInterface $file)
    {
        $this->file = $file;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFile(): UploadedFileInterface
    {
        return $this->file;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

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
        if (empty($this->fileName)){
            $this->fileName = Str::random().($extension?'.'.$extension:'');
        }
        return $this->fileName;
    }
}
