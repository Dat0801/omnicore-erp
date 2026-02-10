<?php

namespace App\Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:product_categories,id'],
            'description' => ['nullable', 'string'],
            'display_order' => ['integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'icon' => ['nullable', 'image', 'max:2048'], // 2MB Max
        ];
    }
}
