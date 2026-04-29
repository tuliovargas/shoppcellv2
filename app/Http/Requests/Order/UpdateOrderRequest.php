<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'status' => 'required|in:waiting_approval,approved,waiting_product,concluded,canceled,waiting_payment,is_budget,is_request,maintenance,returned,waiting_maintenance,partially_returned',
            'client_id' => 'required|integer',
            'user_id' => 'required|integer',
            'products' => 'required|array',
            'note' => 'nullable|string',
        ];
    }
}
