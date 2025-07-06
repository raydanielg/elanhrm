<?php

namespace App\Http\Requests\SuperAdmin\FrontSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFrontSettings extends FormRequest
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
            'social_links.*' => 'nullable|url'
        ];
    }

    public function messages()
    {
        return [
            'social_links.*.url' => __('validation.urlFormat')
        ];
    }

}
