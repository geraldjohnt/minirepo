<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class EmailFeedbackRequest extends Request
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
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required'
        ];
    }

    /**
     * Custom error message.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'お名前が未入力です',
            'email.required' => 'メールアドレスが未入力です',
            'message.required' => 'フィードバック内容が未入力です'
        ];
    }
}
