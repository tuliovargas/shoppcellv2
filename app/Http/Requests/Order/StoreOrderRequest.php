<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
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
            'subtotal' => 'required|numeric',
            'discount' => 'required|numeric',
            'total' => 'required|numeric',
            'status' => 'nullable|in:waiting_approval,approved,waiting_product,waiting_payment,concluded,canceled',
            'client_id' => 'required|integer',
            'user_id' => 'nullable|integer',
            'products' => 'required|array',
            'note' => 'nullable|string',
            'is_warranty' => 'nullable|boolean',
            'seller_id' => 'nullable|integer',
        ];
    }
}
