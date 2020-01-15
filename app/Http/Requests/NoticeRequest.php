<?php

namespace App\Http\Requests;

class NoticeRequest extends Request
{
   /**
     * Rules for each request method.
     *
     * @var array
     */
    protected $rules = [
        'GET'   => [],
        'PUT' => [
            'title' => ['required'],
            'content' => ['required','max:256'],
            'broadcast' => ['required','boolean'],
            'cover' => ['image']
        ],
        'POST'  => [
            'title' => ['required'],
            'content' => ['required','max:256'],
            'broadcast' => ['required','boolean'],
            'cover' => ['image']
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
