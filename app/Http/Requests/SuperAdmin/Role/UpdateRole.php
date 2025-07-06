<?php

namespace App\Http\Requests\SuperAdmin\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRole extends FormRequest
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
            'name' => [
                'required', Rule::unique('roles')
                    ->where('id', '<>', $this->route('role_permission'))
                    ->whereNull('company_id')
            ]
        ];
    }

}
