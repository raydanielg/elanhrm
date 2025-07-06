<?php

namespace App\Http\Requests\Lead;

use App\Http\Requests\CoreRequest;
use App\Traits\CustomFieldsRequestTrait;

class UpdateRequest extends CoreRequest
{
    use CustomFieldsRequestTrait;

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
        \Illuminate\Support\Facades\Validator::extend('check_superadmin', function ($attribute, $value, $parameters, $validator) {
            return !\App\Models\User::withoutGlobalScopes([\App\Scopes\ActiveScope::class, \App\Scopes\CompanyScope::class])
                ->where('email', $value)
                ->where('is_superadmin', 1)
                ->exists();
        });

        $rules = [
            'client_name' => 'required',
            'client_email' => 'nullable|email:rfc,strict|unique:leads,client_email,'.$this->route('lead_contact').',id,company_id,' . company()->id,
        ];

        $rules = $this->customFieldRules($rules);

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];

        $attributes = $this->customFieldsAttributes($attributes);

        $attributes['client_name'] = __('app.name');
        $attributes['client_email'] = __('app.email');

        return $attributes;
    }

    public function messages()
    {
        return [
            'client_email.check_superadmin' => __('superadmin.emailAlreadyExist'),
        ];
    }

}
