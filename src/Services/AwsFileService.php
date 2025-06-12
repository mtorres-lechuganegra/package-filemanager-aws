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
     * @param string $acl
     * @param int $ttlMinutes
     * @param string|null $folder
     * @return array
     */
    public function generateUploadUrl(string $originalFilename, string $contentType, string $acl, int $ttlMinutes = 1, ?string $folder = null): array
    {
        $filename = Str::uuid() . '_' . $originalFilename;
        $path = $folder ? trim($folder, '/') . '/' . $filename : $filename;

        // Detecta si se deben usar credenciales explícitas
        $useExplicitCredentials = app()->environment('local') || app()->environment('qa');

        // Armamos variable de configuración
        $config = [
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest'
        ];

        if ($useExplicitCredentials) {
            $config['credentials'] = [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret')
            ];
        }

        // Construir Cliente S3
        $client = new S3Client($config);

        // Procesar datos
        $data = [
            'Bucket' => config('filesystems.disks.s3.bucket'),
            'Key' => $path,
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

        return [
            'url' => $url,
            'filename' => $filename,
            'path' => $path
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
