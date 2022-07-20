<?php

namespace App\Http\Requests;

use App\Models\VideoStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVideoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->video->user_id === $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $availableStatuses = VideoStatus::all()->pluck('id')->toArray();

        return [
            'video_status_id' => [
                'nullable',
                'integer',
                Rule::in($availableStatuses)
            ],
            'title'           => 'nullable|string|max:255',
            'description'     => 'nullable|string|max:2500',
            'views'           => 'nullable|integer',
            'tags'            => 'nullable|array'
        ];
    }
}
