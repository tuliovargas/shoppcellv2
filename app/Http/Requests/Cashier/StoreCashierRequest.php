<?php

namespace App\Http\Requests\Cashier;

use Illuminate\Foundation\Http\FormRequest;

class StoreCashierRequest extends FormRequest
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
            'type' => 'required|in:in,out',
            'total_value' => 'required|numeric',
            'note' => 'nullable|string',
            'order_id' => 'nullable|integer',
            'expense_id' => 'nullable|integer',
            'user_id' => 'required|integer',
            'coupon_id' => 'nullable|integer',
            'payment_methods.*.id' => 'required|integer',
            'payment_methods.*.value' => 'required|numeric',
            'payment_methods.*.charge' => 'nullable|numeric',
            'payment_methods.*.tax_installment_id' => 'required|integer',
            'payment_methods.*.brand_card' => 'nullable|string|in:mastercard, visa, american_express, elo',
            'payment_methods.*.pix_number' => 'nullable|string',
            'payment_methods.*.check_number' => 'nullable|string',
            'payment_methods.*.check_name' => 'nullable|string',
            'payment_methods.*.bank_id' => 'nullable|integer',
        ];
    }
}
