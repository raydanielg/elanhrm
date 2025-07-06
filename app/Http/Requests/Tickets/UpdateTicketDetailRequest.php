<?php

namespace App\Http\Requests\Tickets;

use App\Http\Requests\CoreRequest;

class UpdateTicketDetailRequest extends CoreRequest
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
            'subject' => 'required',
        ];

        // Only validate description if it's present
        if ($this->has('description')) {
            $rules['description'] = 'required';
        }

        return $rules;
    }

}
