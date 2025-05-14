<?php

namespace LechugaNegra\AwsFileManager\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use LechugaNegra\AwsFileManager\Http\Requests\AwsFilePathRequest;
use LechugaNegra\AwsFileManager\Http\Requests\AwsFileUploadRequest;
use LechugaNegra\AwsFileManager\Services\AwsFileService;

class AwsFileController extends Controller
{
    protected $awsFileService;

    public function __construct(AwsFileService $awsFileService)
    {
        $this->awsFileService = $awsFileService;
    }

    /**
     * Genera una URL temporal de subida para S3.
     *
     * @param AwsFilePathRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateUploadUrl(AwsFileUploadRequest $request)
    {
        $data = $request->input();

        // Procesar valor de acl
        if (empty($data['acl']) && config('filesystems.disks.s3.visibility')) {
            $data['acl'] = config('filesystems.disks.s3.visibility');
        }

        // Solicitar generación de URL de subida
        $result = $this->awsFileService->generateUploadUrl(
            $data['filename'],
            $data['content_type'],
            $data['acl'] ?? '',
            config('filesystems.disks.ttl.url_upload', 1)
        );
        return response()->json([
            'upload' => $result
        ]);
    }

    /**
     * Genera una URL temporal de descarga para S3.
     *
     * @param AwsFilePathRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDownloadUrl(AwsFilePathRequest $request)
    {
        $url = $this->awsFileService->generateDownloadUrl(
            $request->input('path'),
            config('filesystems.disks.ttl.url_download', 1)
        );
        return response()->json([
            'download' => [
                'url' => $url
            ]
        ]);
    }

    /**
     * Elimina un archivo de S3.
     *
     * @param AwsFilePathRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(AwsFilePathRequest $request)
    {
        $deleted = $this->awsFileService->deleteFile($request->input('path'));
        return response()->json([
            'deleted' => $deleted
        ]);
    }
}
