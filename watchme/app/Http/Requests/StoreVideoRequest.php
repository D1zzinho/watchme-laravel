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
            'hash_id'     => 'required|unique:videos,hash_id|string|max:15',
            'title'       => 'required|string|max:255',
            'description' => 'required|string|max:2500',
            'thumbnail'   => 'required|string|max:255',
            'preview'     => 'required|string|max:255',
            'width'       => 'nullable|integer',
            'height'      => 'required|integer',
            'duration'    => 'nullable|integer',
            'file'        => 'required|file|mimetypes:video/avi,video/mpeg,video/quicktime,video/mp4,video/ogg,video/webm,video/x-flv'
        ];
    }
}
