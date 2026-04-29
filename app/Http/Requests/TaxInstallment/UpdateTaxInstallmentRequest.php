<?php

namespace App\Http\Requests\TaxInstallment;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaxInstallmentRequest extends FormRequest
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
            'tax_installments.*' => 'required|numeric',
        ];
    }
}
