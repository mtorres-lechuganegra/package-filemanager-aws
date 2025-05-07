<?php

use Illuminate\Support\Facades\Route;
use LechugaNegra\AwsFileManager\Http\Controllers\AwsFileController;

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('api/file/aws')->name('api.aws.file.')->group(function () {
        Route::post('/url-upload', [AwsFileController::class, 'generateUploadUrl'])->name('url_upload');
        Route::post('/url-download', [AwsFileController::class, 'generateDownloadUrl'])->name('url_download');
        Route::delete('/delete', [AwsFileController::class, 'delete'])->name('delete');
    });
});
