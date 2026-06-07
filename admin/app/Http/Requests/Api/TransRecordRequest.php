<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class TransRecordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'date' => 'nullable|integer',
            'type' => 'required|integer',
            'api_type' => 'nullable|string',
            'pagesize' => 'nullable|integer',
        ];
    }
}
