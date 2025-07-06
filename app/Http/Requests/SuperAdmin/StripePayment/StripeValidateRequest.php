<?php

namespace App\Http\Requests\SuperAdmin\StripePayment;

use Illuminate\Foundation\Http\FormRequest;

class StripeValidateRequest extends FormRequest
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
            'clientName' => 'required',
            'line1' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
        ];
    }

}
