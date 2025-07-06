<?php

namespace App\Http\Requests\SuperAdmin\FeatureTranslation;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'language_setting_id' => 'required',
            'feature_title' => 'required',
            'feature_app_title' => 'required',
        ];
    }

}
