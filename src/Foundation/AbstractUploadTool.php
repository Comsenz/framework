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
    protected $uploadName = '';

    /**
     * @var string
     */
    protected $uploadPath = '';

    /**
     * @var string
     */
    protected $fullPath = '';

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
    public function saveFile($uploadPath, $uploadName, $options = [])
    {
        $options = is_string($options)
            ? ['visibility' => $options]
            : (array) $options;

        $path = trim($uploadPath.'/'.$uploadName, '/');

        $stream = $this->file->getStream();

        if ($this->file->getSize() > 10*1024*1024)
        {
            $resource = $stream->detach();

            $result = $this->driver->putStream($path, $resource, $options);

            if (is_resource($resource)) {
                fclose($resource);
            }

        } else {
            $result = $this->driver->put($path, $stream->getContents(), $options);

            $stream->close();
        }

        return $result ? $this->fullPath = $path : false;
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
    public function getUploadName(string $extension = '', $reset = false)
    {
        if ($reset)
        {
            $this->uploadName = '';
        }

        if (empty($this->uploadName))
        {
            $this->uploadName = Str::random().($extension?'.'.$extension:'');
        }

        return $this->uploadName;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadPath(string $path = '', $reset = false)
    {
        if ($reset)
        {
            $this->uploadPath = '';
        }

        if (empty($this->uploadPath))
        {
            $this->uploadPath = ($path?$path:$this->type);
        }

        return $this->uploadPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getFullName()
    {
        return $this->fullPath;
    }
}
