<?php

namespace App\Http\Requests\SuperAdmin\Register;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        \Illuminate\Support\Facades\Validator::extend('check_superadmin', function ($attribute, $value, $parameters, $validator) {
            return !\App\Models\User::withoutGlobalScopes([\App\Scopes\ActiveScope::class, \App\Scopes\CompanyScope::class])
                ->where('email', $value)
                ->where('is_superadmin', 1)
                ->exists();
        });

        $company = Company::where('hash', request()->company_hash)->firstOrFail();
        $global = global_setting();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc|check_superadmin|unique:users,email,null,id,company_id,' . $company->id,
            'password' => 'required|min:8',
        ];

        if ($global && $global->sign_up_terms == 'yes') {
            $rules['terms_and_conditions'] = 'required';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'email.check_superadmin' => __('superadmin.emailAlreadyExist'),
        ];
    }

}
