<?php

namespace App\Http\Requests\SuperAdmin\FeatureSetting;

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
        $rules = [
            'title' => 'required',
            'language' => 'required'
        ];

        if(request('type') == 'icon')
        {
            $rules['icon'] = 'required';
        }
        elseif(request('type') == 'image' || request('type') == 'apps'){
            
            if (request('image_delete') == 'yes')
            {
                $rules['image'] = 'required';
            }
        }



        if(request('type') != 'apps')
        {
            $rules['description'] = 'required';
        }

        return $rules;
    }

}
