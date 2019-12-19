<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Foundation;

use Discuz\Contracts\Tool\UploadTool;
use Discuz\Http\Exception\UploadVerifyException;
use Illuminate\Contracts\Filesystem\Factory as FileFactory;
use Illuminate\Support\Str;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

abstract class AbstractUploadTool implements UploadTool
{
    /**
     * @var FileFactory
     */
    protected $driver;

    /**
     * @model UploadedFileInterface
     */
    protected $file;

    /**
     * @var string
     */
    protected $extension = '';

    /**
     * @var string
     */
    protected $uploadName = '';

    /**
     * @var string
     */
    protected $uploadPath = 'attachment';

    /**
     * @var string
     */
    protected $fullPath = '';

    /**
     * @var array
     */
    protected $fileType = [];

    /**
     * @var int
     */
    protected $fileSize = 5*1024*1024;

    /**
     * @var array
     */
    protected $options = ['visibility' => 'public'];

    /**
     * @var int
     */
    protected $error = 0;

    public function __construct(FileFactory $driver)
    {
        $this->driver = $driver;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(UploadedFileInterface $file, $uploadPath = '', $uploadName = '', $options = [])
    {
        $this->file = $file;

        $this->extension = pathinfo($this->file->getClientFilename(), PATHINFO_EXTENSION);

        $this->uploadPath = $uploadPath?:$this->uploadPath;

        $this->uploadName = $uploadName?:Str::random().'.'.$this->extension;

        $this->options = is_string($options)
            ? ['visibility' => $options]
            : $options?:$this->options;

        $this->fullPath = trim($this->uploadPath.'/'.$this->uploadName, '/');

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UploadVerifyException
     */
    public function save(array $type = [], int $size = 0)
    {
        $this->verifyFileType($type);

        $this->verifyFileSize($size);

        if ($this->error) {
            throw new UploadVerifyException();
        }

        $stream = $this->file->getStream();

        if ($this->file->getSize() > 10*1024*1024) {
            $resource = $stream->detach();

            $result = $this->driver->putStream($this->fullPath, $resource, $this->options);

            if (is_resource($resource)) {
                fclose($resource);
            }
        } else {
            $result = $this->driver->put($this->fullPath, $stream->getContents(), $this->options);

            $stream->close();
        }

        return $result ? new UploadedFile(
            $this->driver->path($this->fullPath),
            $this->file->getClientFilename(),
            $this->file->getClientMediaType(),
            $this->file->getSize(),
            $this->file->getError(),
            true
                ) : false;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UploadVerifyException
     */
    public function verifyFileType(array $type = [])
    {
        $this->error = 0;

        $type = $type ?: $this->fileType;

        if (! in_array($this->extension, $type)) {
            throw new UploadVerifyException('file_type_not_allow');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws UploadVerifyException
     */
    public function verifyFileSize(int $size = 0)
    {
        $this->error = 0;

        $size = $size ?: $this->fileSize;

        if ($this->file->getSize() > $size) {
            throw new UploadVerifyException('file_size_not_allow');
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadName()
    {
        return $this->uploadName;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadPath()
    {
        return $this->uploadPath;
    }

    /**
     * {@inheritdoc}
     */
    public function getUploadFullPath()
    {
        return $this->fullPath;
    }
}
