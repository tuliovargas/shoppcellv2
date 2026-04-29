<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
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
            'payday' => 'required|date',
            'value' => 'nullable|numeric',
            'installments' => 'required|numeric',
            'observation' => 'nullable|string',
            'payment_method_id' => 'required|integer',
            'expense_type_id' => 'required|integer',
            'supplier_id' => 'required|integer',
            'receipt.*' => 'required|image'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'É obrigatório informar um nome à despesa',
            'installments.required' => 'É obrigatório informar a quantidade de parcelas da despesa',
            'payment_method_id.required' => 'É obrigatório selecionar o meio de pagamento',
            'expense_type_id.required' => 'É obrigatório selecionar o tipo de despesa',
            'supplier_id.required' => 'É obrigatório selecionar o fornecedor',
            'receipt.image' => 'O anexo enviado deve ser uma imagem (PNG, JPG, GIF)',
            'payday.required' => 'É obrigatório informar a data da despesa'
        ];
    }
}
