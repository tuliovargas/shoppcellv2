<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'barcode' => 'nullable|string',
            'name' => 'required|string',
            'photo' => 'nullable|image',
            'price' => 'nullable|numeric',
            'cost' => 'nullable|numeric',
            'observation' => 'nullable|string',
            'minimum_stock' => 'nullable|integer',
            'can_discount' => 'nullable',
            'can_commission' => 'nullable',
            'commission_percentage' => 'nullable|numeric',
            'technician_commission_percentage' => 'nullable|numeric',
            'discount_percentage' => 'nullable|numeric',
            'is_new' => 'nullable',
            'is_active' => 'nullable',
            'type' => 'required|in:un,sv,kg,other',
            'days_warranty' => 'required|integer',
            'brand_id' => 'required|integer',
            'category_id' => 'required|integer',
            'sub_category' => 'nullable|integer',
            'brand_model' => 'nullable|string',
            'quantity_in_stock' => 'nullable|integer',
        ];
    }
}
