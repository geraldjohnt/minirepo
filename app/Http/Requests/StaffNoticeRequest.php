<?php

namespace App\Http\Requests;

use Auth;

class StaffNoticeRequest extends Request
{
    /**
     * Rules for each request method.
     *
     * @var array
     */
    protected $rules = [
        'GET' => [],
        'PUT' => [
            'read' => ['required']
        ],
        'POST' => [
            'read' => ['required']
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
        // print_r($this->route()->parameter('notices')); die();
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
