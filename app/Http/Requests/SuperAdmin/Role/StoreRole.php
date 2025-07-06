<?php

namespace App\Http\Requests\SuperAdmin\Role;

use App\Http\Requests\CoreRequest;
use Illuminate\Validation\Rule;

class StoreRole extends CoreRequest
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
            'name' => ['required', Rule::unique('roles')->whereNull('company_id')]
        ];
    }

}
