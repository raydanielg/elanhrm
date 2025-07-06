<?php

namespace App\Http\Requests\SuperAdmin\Billing;

use Illuminate\Foundation\Http\FormRequest;

class OfflinePaymentRequest extends FormRequest
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
     * @return string[]
     */
    public function rules()
    {
        return [
            'slip' => 'required|mimes:jpg,png,jpeg,pdf,doc,docx,rtf',
            'description' => 'required'
        ];
    }

    public function attributes()
    {
        return [
            'slip' => __('app.receipt')
        ];
    }

}
