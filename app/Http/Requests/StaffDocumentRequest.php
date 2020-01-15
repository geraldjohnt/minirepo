<?php

namespace App\Http\Requests;

class StaffDocumentRequest extends Request
{
    /**
     * Rules for each request method.
     *
     * @var array
     */
    
    protected $rules = [
        'GET' => [],
        'PUT' => [
            'title' => ['required'],
            'description' => ['max:256'],
            'allow_download' => ['required','boolean'],
        ],
        'POST' => [
            'title' => ['required'],
            'description' => ['max:256'],
            'allow_download' => ['required','boolean'],
            'file.*' => ['required','file','max:10240','mimes:pdf,doc,ppt,xlsx,xls,docx'],
            'folder_id' => ['required']
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'タイトルフィールドは必須です。',
            'description.max' => '説明は256文字を超えることはできません。',
            'file.max' => 'アップロード出来るファイルサイズは10mbまでとなっております。',
            'file.mimes' => 'ファイルは、pdf、doc、ppt、xlsx、xls、docxタイプのファイルである必要があります。'
        ];
    }
}
