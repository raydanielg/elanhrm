<?php

namespace App\Http\Requests\SuperAdmin\ContactUs;

use GuzzleHttp\Client;
use App\Models\GlobalSetting;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class ContactUsRequest extends FormRequest
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
        $global = GlobalSetting::first();
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ];

        if($global->google_recaptcha_v2_status == 'active'){
            $rules['g-recaptcha-response'] = 'required';
        }

        if ($global->google_recaptcha_v3_status == 'active') {
            $rules['g_recaptcha'] = Rule::prohibitedIf(function () use ($global) {
                return !$this->validateGoogleRecaptcha($global->google_recaptcha_v3_secret_key, request()->g_recaptcha);
            });
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'g-recaptcha-response.required' => __('superadmin.recaptchaInvalid'),
            'g_recaptcha.prohibited' => __('superadmin.recaptchaInvalid'),
        ];
    }

    public function validateGoogleRecaptcha($secret, $googleRecaptchaResponse)
    {
        $client = new Client();
        $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'form_params' => [
                    'secret' => $secret,
                    'response' => $googleRecaptchaResponse,
                    'remoteip' => $_SERVER['REMOTE_ADDR']
                ]
            ]
        );

        $body = json_decode((string)$response->getBody());

        return $body->success;
    }

}
