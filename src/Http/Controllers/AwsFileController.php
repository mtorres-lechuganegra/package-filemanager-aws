<?php

namespace LechugaNegra\AwsFileManager\Http\Controllers;

use App\Http\Controllers\Controller;
use LechugaNegra\AwsFileManager\Http\Requests\AwsFilePathRequest;
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
    public function generateUploadUrl(AwsFilePathRequest $request)
    {
        $url = $this->awsFileService->generateUploadUrl($request->input('path'));
        return response()->json(['upload_url' => $url]);
    }

    /**
     * Genera una URL temporal de descarga para S3.
     *
     * @param AwsFilePathRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDownloadUrl(AwsFilePathRequest $request)
    {
        $url = $this->awsFileService->generateDownloadUrl($request->input('path'));
        return response()->json(['download_url' => $url]);
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
        return response()->json(['deleted' => $deleted]);
    }
}
