<?php

namespace App\Http\Requests\SuperAdmin\Billing;

use Illuminate\Foundation\Http\FormRequest;

class AuthorizePaymentRequest extends FormRequest
{

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
        return [
            'owner' => 'required',
            'cvv' => 'required',
            'card_number' => 'required',
        ];
    }

}
