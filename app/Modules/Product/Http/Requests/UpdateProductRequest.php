<?php

namespace App\Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sku' => [
                'required',
                'string',
                'max:255',
                Rule::unique('products', 'sku')->ignore($this->product),
            ],
            'barcode' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'in:physical,service,digital'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['nullable', 'string', 'max:50'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'dimensions' => ['nullable', 'array'],
            'allow_backorders' => ['boolean'],
            'tags' => ['nullable'],
            'product_category_id' => ['nullable', 'exists:product_categories,id'],
            'is_active' => ['boolean'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'has_variants' => ['boolean'],
            'variants' => ['nullable', 'array', 'required_if:has_variants,true'],
            'variants.*.id' => ['nullable', 'exists:products,id'],
            'variants.*.sku' => ['required_with:variants', 'string', 'distinct'],
            'variants.*.price' => ['required_with:variants', 'numeric', 'min:0'],
            'variants.*.quantity' => ['nullable', 'integer', 'min:0'],
            'variants.*.low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'variants.*.variant_attributes' => ['required_with:variants', 'array'],
        ];
    }
}
