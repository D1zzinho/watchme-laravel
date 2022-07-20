<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:2500',
            'tags'        => 'nullable|array',
            'file'        => 'required|file|mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4,video/ogg,video/webm,video/x-flv'
        ];
    }
}
