<?php

namespace App\Http\Requests\SuperAdmin\Register;

use App\Models\GlobalSetting;
use App\Models\User;
use App\Models\Company;
use App\Scopes\ActiveScope;
use App\Scopes\CompanyScope;
use Illuminate\Validation\Rule;
use App\Http\Requests\CoreRequest;
use Illuminate\Support\Facades\Validator;

class StoreRequest extends CoreRequest
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


        // This is done to remove request()->merge(['sub_domain' => $subdomain]); and
        // validate on sub_domain part
        if (module_enabled('Subdomain')) {
            if (request()->sub_domain) {
                $subdomain = preg_replace('/\.' . preg_quote(getDomain(), '/') . '/', '', request()->sub_domain, 1);

                if (!preg_match('/^[A-Z][a-zA-Z0-9]+$/i', $subdomain)) {
                    return [
                        'sub_domain' => 'regex:/^[A-Z][a-zA-Z0-9]+$/',
                    ];
                }
            }
        }

        $length = str(getDomain())->length();

        // 1 for dot
        $min = $length + 1 + 4;

        $rules = [
            'company_name' => 'required',
            'name' => 'required',
            'email' => 'required|email:rfc,strict|check_superadmin',
            'sub_domain' => module_enabled('Subdomain') ? 'required|banned_sub_domain|min:' . $min . '|unique:companies,sub_domain|max:50' : '',
        ];

        if (request()->has('password_confirmation')) {
            $rules['password'] = 'required|confirmed|min:8';

        }
        else {
            $rules['password'] = 'required|min:8';
        }

        $global = global_setting();

        if ($global && $global->sign_up_terms == 'yes') {
            $rules['terms_and_conditions'] = 'required';
        }

        if ($global->google_recaptcha_v2_status == 'active') {
            $rules['g-recaptcha-response'] = 'required';
        }

        // if ($global->google_recaptcha_v3_status == 'active') {
        //     $rules['g_recaptcha'] = Rule::prohibitedIf(function () use ($global) {
        //         return !GlobalSetting::validateGoogleRecaptcha(request()->g_recaptcha);
        //     });
        // }

        if (Company::where('company_email', '=', request()->email)->exists()) {
            $rules['email'] = 'required|email:rfc,strict|unique:users,email';
        }

        $user = User::where('users.email', request()->email)->first();

        if ($user) {
            $user->hasRole('employee') ? $rules['email'] = 'required|email:rfc,strict|unique:users' : '';
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'email.check_superadmin' => __('superadmin.emailAlreadyExist'),
            'terms_and_conditions.required' => __('superadmin.superadmin.acceptTerms') . ' ' . __('superadmin.superadmin.termsAndCondition'),
            'g-recaptcha-response.required' => __('superadmin.recaptchaInvalid'),
            'g_recaptcha.prohibited' => __('superadmin.recaptchaInvalid'),
            'sub_domain.regex' => __('superadmin.validationSubDomain'),
            'sub_domain.min' => __('validation.min.string', ['min' => 4])
        ];
    }

    public function prepareForValidation()
    {
        if (empty($this->sub_domain)) {
            return;
        }

        // Add servername domain suffix at the end
        $subdomain = trim($this->sub_domain, '.') . '.' . getDomain();
        $this->merge(['sub_domain' => $subdomain]);
        request()->merge(['sub_domain' => $subdomain]);
    }

}
