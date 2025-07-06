<?php

namespace App\Http\Requests\SuperAdmin\FrontSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFrontMenu extends FormRequest
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
            'home' => 'required',
            'feature' => 'required',
            'contact' => 'required',
            'price' => 'required',
            'get_start' => 'required',
            'login' => 'required',
            'contact_submit' => 'required',
        ];
    }

}
