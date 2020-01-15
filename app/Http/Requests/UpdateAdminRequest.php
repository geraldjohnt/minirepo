<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class UpdateAdminRequest extends Request
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
            // 'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'avatar' => 'image',
            'current_password' => ['required_with:password', 'min:6'],
            'password' => ['required_with:password_confirmation', 'min:6'],
            'password_confirmation' => ['min:6', 'same:password']
        ];
    }

}
