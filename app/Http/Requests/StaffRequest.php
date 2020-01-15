<?php

namespace App\Http\Requests;

class StaffRequest extends Request
{
    /**
     * Rules for each request method.
     *
     * @var array
     */
    protected $rules = [
        'GET' => [],
        'PUT' => [
            // 'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required','email'],
            'avatar' => ['image'],
            // 'current_password' => ['required', 'min:6'],
            'password' => ['required_with:password_confirmation', 'min:6'],
            'password_confirmation' => ['min:6', 'same:password']
        ],
        'POST' => [
            // 'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required','email'],
            'avatar' => ['image'],
            'password' => ['required_with:password_confirmation', 'min:6'],
            'password_confirmation' => ['required', 'min:6', 'same:password']
        ],
        'DELETE' => [],
    ];

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
        return $this->rules[$this->method()];
    }
}
