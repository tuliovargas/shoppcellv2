<?php

namespace App\Http\Requests\Coupon;

use Illuminate\Foundation\Http\FormRequest;

class StoreCouponRequest extends FormRequest
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
            'name' => 'required|string|unique:coupons',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'quantity' => 'required|integer',
            'value' => 'required|numeric',
            'user_id' => 'required|integer',
        ];
    }
}
