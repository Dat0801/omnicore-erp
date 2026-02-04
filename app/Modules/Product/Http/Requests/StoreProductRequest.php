<?php

namespace App\Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:products,code'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'product_category_id' => ['nullable', 'exists:product_categories,id'],
            'is_active' => ['boolean'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'], // 2MB Max
        ];
    }
}
