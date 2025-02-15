<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTranslationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'key' => [
                'required',
                'string',
                Rule::unique('translations')->where(function ($query) {
                    return $query->where('locale', $this->input('locale'));
                }),
            ],
            'content' => 'required|string',
            'locale' => 'required|string',
            'tag_id' => 'required|exists:tags,id'
        ];
    }
}
