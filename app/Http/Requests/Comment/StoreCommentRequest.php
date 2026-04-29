<?php

namespace App\Http\Requests\Comment;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
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
            'text' => 'required|string',
            'user_id' => 'required|integer',
            'order_id' => 'required|integer',
            'file' => 'nullable|file',
            'uploadable_id' => 'required_if:file,!==,null|integer',
            'uploadable_type' => 'required_if:file,!==,null|string',
        ];
    }
}
