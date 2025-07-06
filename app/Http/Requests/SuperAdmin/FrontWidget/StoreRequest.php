<?php

namespace App\Http\Requests\SuperAdmin\FrontWidget;

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
       
        $rules = [
            'name'    => 'required',
        ];

        if(is_null($this->header_script) && is_null($this->footer_script)) {
            $rules['header_script'] = 'required';
            $rules['footer_script'] = 'required';
        }

        return $rules;
        
    }
    
}
