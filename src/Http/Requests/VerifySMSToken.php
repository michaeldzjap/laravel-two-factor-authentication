<?php

namespace MichaelDzjap\TwoFactorAuth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifySMSToken extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if ($this->session()->has('two-factor:auth')) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'token' => ['required', 'string', 'regex:/^\d+$/'],
        ];
    }
}
