<?php

namespace App\Http\Requests\OfflinePaymentSetting;

use App\Http\Requests\CoreRequest;

class UpdateRequest extends CoreRequest
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
        $rules = [
            'description' => 'required',
        ];

        if (company()) {
            $rules['name'] = 'required|unique:offline_payment_methods,name,'.$this->route('offline_payment_setting').',id,company_id,' . company()->id;
        }
        else{
            $rules['name'] = 'required|unique:offline_payment_methods,name,'.$this->route('global_offline_payment_setting').',id,company_id,null';
        }

        return $rules;
    }

}
