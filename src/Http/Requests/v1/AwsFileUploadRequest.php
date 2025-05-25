<?php

namespace LechugaNegra\AwsFileManager\Http\Requests\v1;

use Illuminate\Foundation\Http\FormRequest;

class AwsFileUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'filename' => 'required|string',
            'content_type' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'filename.required' => 'The filename is required.',
            'content_type.required' => 'The content type is required.',
        ];
    }
}
