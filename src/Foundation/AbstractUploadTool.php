<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Foundation;

use App\Models\Attachment;
use Discuz\Contracts\Tool\UploadTool;
use Discuz\Filesystem\CosAdapter;
use Discuz\Http\Exception\UploadVerifyException;
use Illuminate\Contracts\Filesystem\Factory as FileFactory;
use Illuminate\Contracts\Filesystem\FileExistsException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\Factory as ContractsFilesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Psr\Http\Message\UploadedFileInterface;

abstract class AbstractUploadTool implements UploadTool
{
    /**
     * @var FileFactory
     */
    protected $filesystem;

    /**
     * @var ContractsFilesystem
     */
    protected $contractsFilesystem;

    /**
     * @var UploadedFileInterface
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
    protected $isRemote = '';

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
    protected $fileSize = 5 * 1024 * 1024;

    /**
     * @var array
     */
    protected $options = [
        'visibility' => 'public'
    ];

    /**
     * @var int
     */
    protected $error = 0;

    public function __construct(Filesystem $filesystem, ContractsFilesystem $contractsFilesystem)
    {
        $this->filesystem = $filesystem;
        $this->contractsFilesystem = $contractsFilesystem;
    }

    /**
     * 删除附件
     *
     * @param Attachment $attachment
     * @return bool
     */
    public function delete(Attachment $attachment)
    {
        $path = $attachment->file_path . '/' . $attachment->attachment;

        $remote = $attachment->is_remote;

        $result = $this->contractsFilesystem->disk($remote ? 'attachment_cos' : 'attachment')->delete($path);

        // 如果是帖子图片,删除本地缩略图
        if ($attachment->type == Attachment::TYPE_OF_IMAGE) {
            $thumb = $attachment::replaceThumb($path);
            $this->contractsFilesystem->disk('attachment')->delete($thumb);
        }

        return $result;
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

        if (!in_array($this->extension, $type) || $this->extension == 'php') {
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

    /**
     * @return bool
     */
    public function getIsRemote()
    {
        $this->isRemote = $this->isRemote ?: $this->filesystem->getAdapter() instanceof CosAdapter;
        return $this->isRemote;
    }
}
