<?php

namespace App\Http\Requests\SuperAdmin\Company;

use App\Models\CustomField;
use App\Models\User;
use App\Scopes\ActiveScope;
use App\Scopes\CompanyScope;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
        Validator::extend('check_superadmin', function ($attribute, $value, $parameters, $validator) {
            return !User::withoutGlobalScopes([ActiveScope::class, CompanyScope::class])
                ->where('email', $value)
                ->where('is_superadmin', 1)
                ->exists();
        });

        $regex = '/^[a-zA-Z0-9\-]+$/';

        if (!$this->domain) {
            $regex = '/^([a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,8})$/';
        }

        $rules = [
            'company_name' => 'required',
            'company_email' => 'required|email|unique:companies',
            'sub_domain' => module_enabled('Subdomain') ? [
                'required',
                'banned_sub_domain',
                ($this->domain ? 'min:1' : 'min:4'),
                'max:75',
                'regex:' . $regex,
                Rule::unique('companies')->where(function ($query) {
                    return $query->where('sub_domain', $this->sub_domain)
                        ->orWhere('sub_domain', $this->sub_domain . $this->domain);
                }),
            ] : '',
            'status' => 'required',
            'email' => 'required|email:rfc,strict|check_superadmin',
            'name' => 'required|min:2',

        ];

        if (request()->get('website')) {
            $rules['website'] = 'required|url';
        }

        if (request()->get('custom_fields_data')) {
            $fields = request()->get('custom_fields_data');

            foreach ($fields as $key => $value) {
                $idarray = explode('_', $key);
                $id = end($idarray);
                $customField = CustomField::findOrFail($id);

                if ($customField->required == 'yes' && (is_null($value) || $value == '')) {
                    $rules['custom_fields_data[' . $key . ']'] = 'required';
                }
            }
        }

        return $rules;

    }

    public function messages()
    {
        return [
            'email.check_superadmin' => __('superadmin.emailAlreadyExist'),
        ];
    }

    public function attributes()
    {
        $attributes = [
            'sub_domain' => __('subdomain::app.core.domain'),
        ];

        if (request()->get('custom_fields_data')) {
            $fields = request()->get('custom_fields_data');

            foreach ($fields as $key => $value) {
                $idarray = explode('_', $key);
                $id = end($idarray);
                $customField = CustomField::findOrFail($id);

                if ($customField->required == 'yes') {
                    $attributes['custom_fields_data[' . $key . ']'] = $customField->label;
                }
            }
        }

        return $attributes;
    }

}
