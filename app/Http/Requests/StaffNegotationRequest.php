<?php

namespace App\Http\Requests;

class StaffNegotationRequest extends Request
{
    /**
     * Rules for each request method.
     *
     * @var array
     */
    protected $rules = [
        'GET' => [],
        'PUT' => [
            // 'notes' => ['max:256']
        ],
        'POST' => [
            // 'notes' => ['max:256']
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
