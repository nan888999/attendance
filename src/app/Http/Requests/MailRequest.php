<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => 'required | string | email | unique:users',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'メールアドレスを入力してください',
            'email.string' => '文字列で入力してください',
            'email.email' => 'メール形式で入力してください',
            'email.unique' => 'そのメールアドレスは登録済みです',
        ];
    }
}

