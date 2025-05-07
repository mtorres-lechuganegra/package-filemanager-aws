<?php

namespace LechugaNegra\AwsFileManager\Services;

use Illuminate\Support\Facades\Storage;

class AwsFileService
{
    protected string $disk = 's3';

    /**
     * Genera una URL temporal de subida para S3.
     *
     * @param string $path
     * @param int $ttlMinutes
     * @return string
     */
    public function generateUploadUrl(string $path, int $ttlMinutes = 10): string
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($this->disk);

        return $disk->temporaryUrl(
            $path,
            now()->addMinutes($ttlMinutes),
            ['ResponseContentDisposition' => 'attachment']
        );
    }

    /**
     * Genera una URL temporal de descarga para S3.
     *
     * @param string $path
     * @param int $ttlMinutes
     * @return string
     */
    public function generateDownloadUrl(string $path, int $ttlMinutes = 10): string
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($this->disk);
        
        return $disk->temporaryUrl(
            $path,
            now()->addMinutes($ttlMinutes)
        );
    }

    /**
     * Elimina un archivo de S3.
     *
     * @param string $path
     * @return bool
     */
    public function deleteFile(string $path): bool
    {
        return Storage::disk($this->disk)->delete($path);
    }
}
