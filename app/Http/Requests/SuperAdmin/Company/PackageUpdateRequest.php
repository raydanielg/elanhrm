<?php

namespace App\Http\Requests\SuperAdmin\Company;

use App\Models\SuperAdmin\Package;
use Illuminate\Foundation\Http\FormRequest;

class PackageUpdateRequest extends FormRequest
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
        $package = Package::find($this->package);

        $rules = [
            'package' => 'required|exists:packages,id',
            'package_type' => 'required|in:monthly,annual,lifetime',
        ];

        if(is_null($package) || $package->default != 'trial')
        {
            $rules['pay_date'] = 'required';
        }

        return $rules;




    }

}
