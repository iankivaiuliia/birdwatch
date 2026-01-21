<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBirdCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        $id = $this->route('category')->id ?? null;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255', "unique:bird_categories,name,$id"],
            'description' => ['nullable', 'string'],
        ];
    }
}
