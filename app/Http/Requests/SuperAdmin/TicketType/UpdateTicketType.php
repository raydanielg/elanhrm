<?php

namespace App\Http\Requests\SuperAdmin\TicketType;

use App\Http\Requests\CoreRequest;

class UpdateTicketType extends CoreRequest
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
            'type' => 'required|unique:support_ticket_types,type,'.$this->route('superadmin.support-ticketTypes'),
        ];
    }

}
