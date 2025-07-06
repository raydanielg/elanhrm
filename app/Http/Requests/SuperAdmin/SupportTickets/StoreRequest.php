<?php

namespace App\Http\Requests\SuperAdmin\SupportTickets;

use App\Traits\CustomFieldsRequestTrait;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    use CustomFieldsRequestTrait;

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
        $rules['subject'] = 'required';
        $rules['description'] = [
            'required',
            function ($attribute, $value, $fail) {
                $comment = trim_editor($value);;

                if ($comment == '') {
                    $fail(__('validation.required'));
                }
            }
        ];
        $rules['priority'] = 'sometimes|required';
        $rules['requested_for'] = 'required';

        $rules = $this->customFieldRules($rules);

        return $rules;
    }

    public function attributes()
    {
        $attributes = [];

        $attributes = $this->customFieldsAttributes($attributes);

        return $attributes;
    }

    public function messages()
    {
        return [
            'requested_for.required' => __('modules.tickets.requesterName').' '.__('app.required')
        ];
    }

}
