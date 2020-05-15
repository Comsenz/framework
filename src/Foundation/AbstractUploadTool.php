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
     * {@inheritdoc}
     */
    public function upload(UploadedFileInterface $file, $uploadPath = '', $uploadName = '', $options = [])
    {
        $this->file = $file;

        $uploadPath = $uploadPath . date('/Y/m/d');

        $this->extension = Str::lower(pathinfo($this->file->getClientFilename(), PATHINFO_EXTENSION));

        $this->uploadPath = $uploadPath ?: $this->uploadPath;

        $fileName = Str::random();

        $this->uploadName = $uploadName ?: $fileName . '.' . $this->extension;

        $this->options = is_string($options)
            ? ['visibility' => $options]
            : ($options ?: $this->options);

        /**
         * @see 云上数据处理 https://cloud.tencent.com/document/product/460/18147#.E4.BA.91.E4.B8.8A.E6.95.B0.E6.8D.AE.E5.A4.84.E7.90.86
         */
        if ($this->file->isGallery && $this->getIsRemote()) {
            $this->options = array_merge($this->options, [
                'header' => [
                    'PicOperations' => json_encode([
                        'rules' => [
                            [
                                'fileid' => md5($fileName) . '_blur.' . $this->extension,
                                'rule' => 'imageMogr2/thumbnail/500x500/blur/35x15',
                            ]
                        ],
                    ]),
                ]
            ]);
        }

        $this->fullPath = trim($this->uploadPath . '/' . $this->uploadName, '/');

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $type
     * @param int $size
     * @return bool|array
     * @throws UploadVerifyException
     * @throws FileExistsException
     */
    public function save(array $type = [], int $size = 0)
    {
        $this->verifyFileType($type);

        $this->verifyFileSize($size);

        if ($this->error) {
            throw new UploadVerifyException();
        }

        $stream = $this->file->getStream();

        if ($this->file->getSize() > 10 * 1024 * 1024) {
            $resource = $stream->detach();
            $result = $this->filesystem->writeStream($this->fullPath, $resource, $this->options);

            if (is_resource($resource)) {
                fclose($resource);
            }
        } else {
            $result = $this->filesystem->put($this->fullPath, $stream->getContents(), $this->options);

            $stream->close();
        }

        $isRemote = [
            'isRemote' => $this->getIsRemote()
        ];
        if ($this->getIsRemote()) {
            $fileInfo = [
                'url' =>  $this->filesystem->getAdapter()->getSourcePath($this->fullPath),
                'path' => $this->fullPath
            ];
        } else {
            $fileInfo = [
                'url' => $this->filesystem->url($this->fullPath),
                'path' => $this->filesystem->path($this->fullPath)
            ];
        }
        return $result ? Arr::collapse([$isRemote, $fileInfo]) : false;

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
        if ($attachment->is_gallery) {
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
