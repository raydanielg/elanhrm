<?php

namespace App\Http\Requests\SuperAdmin\FrontSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDetail extends FormRequest
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
            'header_title' => 'required',
            'header_description' => 'required',
            'feature_title' => 'sometimes|required',
            'feature_description' => 'sometimes|required',
            'price_title' => 'sometimes|required',
            'price_description' => 'sometimes|required',
        ];
    }

}
