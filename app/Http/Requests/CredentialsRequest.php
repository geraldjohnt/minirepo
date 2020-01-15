<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CredentialsRequest extends Request
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
            'trial' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'password' => 'required'
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
            'trial.required' => 'お試しを選択していません',
            'last_name.required' => 'お名前が未入力です',
            'email.required' => 'メールアドレスが未入力です',
            'password.required' => 'パスワードを入力していません'
        ];
    }
}
