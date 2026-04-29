<?php

namespace App\Http\Requests\Stock;

use Illuminate\Foundation\Http\FormRequest;

class StoreStockRequest extends FormRequest
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
            'nf_purchase' => 'required|string',
            'note' => 'nullable|string',
            'supplier_id' => 'required|integer',
            'payment_method_id' => 'required|integer',
            'products' => 'required|array',
        ];
    }
}
