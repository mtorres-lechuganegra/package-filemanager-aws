<?php

namespace LechugaNegra\AwsFileManager\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AwsFilePathRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Ajusta esta lógica si deseas restringir acceso según roles o policies
        return true;
    }

    public function rules(): array
    {
        return [
            'path' => 'required|string',
        ];
    }
    
    public function messages(): array
    {
        return [
            'path.required' => 'The file path is required.',
            'path.string'   => 'The file path must be a valid string.',
        ];
    }
}
