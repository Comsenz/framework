<?php

/**
 * Discuz & Tencent Cloud
 * This is NOT a freeware, use is subject to license terms
 */

namespace Discuz\Http;

use Illuminate\Http\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Laminas\Diactoros\Response;

class FileResponse extends Response
{
    protected $file;

    public function __construct($file, int $status = 200, array $headers = [])
    {
        $this->setFile($file);
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = $this->file->getMimeType() ?: 'application/octet-stream';
        }
        $body = $this->createBody();

        parent::__construct($body, $status, $headers);
    }

    protected function createBody()
    {
        return fopen($this->file->getRealPath(), 'rb');
    }

    /**
     * Sets the file to stream.
     *
     * @param \SplFileInfo|string $file               The file to stream
     * @param string              $contentDisposition
     * @param bool                $autoEtag
     * @param bool                $autoLastModified
     *
     * @return $this
     *
     * @throws FileException
     */
    public function setFile($file)
    {
        if (!$file instanceof \Symfony\Component\HttpFoundation\File\File) {
            if ($file instanceof \SplFileInfo) {
                $file = new File($file->getPathname());
            } else {
                $file = new File((string) $file);
            }
        }

        if (!$file->isReadable()) {
            throw new FileException('File must be readable.');
        }

        $this->file = $file;

        return $this;
    }
}
