<?php

namespace App\Http\Requests\Supplier;

use App\Models\Supplier;
use App\Services\Utilities\OnlyNumbersService;
use Illuminate\Foundation\Http\FormRequest;
use PHPUnit\Util\Json;
use Psy\Util\Json as UtilJson;

class SupplierRequest extends FormRequest
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
            'cnpj' => ['required', 'string', function ($attribute, $value, $fail) {
                if ($this->id) {
                    $cnpj = preg_replace('/[^0-9]/', '', $value);
                    $find = Supplier::query()->where('cnpj', '=', $cnpj)->where('id', '!=', $this->id)->first();
                    if ($find) {
                        $fail('Já existe um fornecedor cadastrado com este CNPJ');
                    }
                }
            }],
            'photo' => 'mimes:jpg,jpeg,png',
            'state_registration' => 'string|nullable',
            'cellphone' => 'string|nullable',
            'phone' => 'string|nullable',
            'responsible_person' => 'string|nullable',
            'observation' => 'string|nullable',
            'postcode'  => 'string|nullable|size:9',
            'street' => 'string|nullable',
            'neighborhood' => 'string|nullable',
            'number' => 'numeric|nullable',
            'complement' => 'string|nullable',
            'city' => 'string|nullable',
            'state' => 'string|nullable|min:2|max:2',
            'id' => 'nullable'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'cnpj' => preg_replace('/[^0-9]/', '', $this->cnpj)
        ]);
    }

    public function messages()
    {
        return [
            'cnpj.unique' => 'Já existe um fornecedor cadastrado com este CNPJ',
            'cnpj.required' =>  'É obrigatório informar um CNPJ',
            'name.required' => 'É obrigatório informar um nome',
            'name.string' => 'É obrigatório informar um nome'
        ];
    }
}
