<?php

namespace LechugaNegra\AwsFileManager\Services;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
    public function generateUploadUrl(string $originalFilename, string $contentType, string $acl, int $ttlMinutes = 1): array
    {
        $filename = Str::uuid() . '_' . $originalFilename;

        // Construir Cliente S3
        $client = new S3Client([
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest',
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
        ]);

        // Procesar datos
        $data = [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $filename,
            'ContentType' => $contentType
        ];
        if ($acl) {
            $data['ACL'] = $acl;
        }

        // Obtener comando
        $command = $client->getCommand('PutObject', $data);

        // Generar url de subida
        $request = $client->createPresignedRequest($command, '+' . $ttlMinutes . ' minutes');
        $url = (string) $request->getUri();

        // Armar ruta pública según ACL
        $publicURL = '';
        if ($acl == 'public-read') {
            $publicURL = config('filesystems.disks.s3.url') . '/' . $filename;
        }

        return [
            'url' => $url,
            'filename' => $filename,
            'public_url' => $publicURL
        ];
    }

    /**
     * Genera una URL temporal de descarga para S3.
     *
     * @param string $path
     * @param int $ttlMinutes
     * @return string
     */
    public function generateDownloadUrl(string $path, int $ttlMinutes = 1): string
    {
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($this->disk);
        
        // Generar URL de descarga
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
        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk($this->disk);

        // Validar existencia de archivo
        if (!$disk->exists($path)) {
            return false;
        }

        // Eliminar archivo del S3
        return $disk->delete($path);
    }
}
