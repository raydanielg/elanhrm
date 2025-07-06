<?php

namespace App\Http\Requests\SuperAdmin\TestimonialSettings;

use Illuminate\Foundation\Http\FormRequest;

class TitleStoreUpdateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'language' => 'required|unique:tr_front_details,language_setting_id,' . $this->id . ',id',
            'testimonial_title' => 'required|unique:tr_front_details,testimonial_title,' . $this->id . ',id,language_setting_id,' . $this->language,
        ];
    }

}
