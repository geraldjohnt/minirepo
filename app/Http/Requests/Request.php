<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Dingo\Api\Exception\ResourceException;
use Illuminate\Contracts\Validation\Validator;

abstract class Request extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw new ResourceException('入力内容に不備があります', $validator->getMessageBag());
    }
}
