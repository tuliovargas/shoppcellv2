<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
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
            'name' => 'required|string',
            'invoice' => 'nullable|string',
            'payday' => 'nullable|date',
            'value' => 'nullable|numeric',
            'installments' => 'required|numeric',
            'observation' => 'nullable|string',
            'payment_method_id' => 'required|integer',
            'expense_type_id' => 'required|integer',
        ];
    }
}
