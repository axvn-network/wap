<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GetGameUrlRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'plat_name' => 'required',
            'game_type' => 'required',
            'game_code' => 'nullable',
            'is_mobile_url' => 'nullable',
        ];
    }
}
