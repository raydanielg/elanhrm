<?php

namespace App\Http\Requests\SuperAdmin\FooterSetting;

use Illuminate\Foundation\Http\FormRequest;

class FooterTextRequest extends FormRequest
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
            'footer_copyright_text' => 'required',
        ];
    }

}
