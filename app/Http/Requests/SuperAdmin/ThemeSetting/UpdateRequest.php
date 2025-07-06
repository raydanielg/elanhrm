<?php

namespace App\Http\Requests\SuperAdmin\ThemeSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        $rules = [];

        if(!$this->has('frontend_disable')) {
            $rules = [
                'custom_homepage_url' => 'nullable|required_if:setup_homepage,custom|url'
            ];
        }

        return $rules;
    }

}
