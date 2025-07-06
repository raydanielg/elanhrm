<?php

namespace App\Http\Requests\SuperAdmin\Packages;

use App\Models\SuperAdmin\GlobalPaymentGatewayCredentials;
use App\Models\SuperAdmin\Package;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $data = [
            'currency_id' => 'required|exists:global_currencies,id',
            'name' => [
                'required',
                Rule::unique('packages')->where(function ($query) {
                    return $query->where('currency_id', $this->get('currency_id'));
                })->ignore($this->route('package')),
            ],
            'max_employees' => 'required|numeric',
            'max_storage_size' => 'required|gte:-1',
            'storage_unit' => 'required|in:gb,mb',
        ];

        $package = Package::find($this->route('package'));

        if(request()->package == 'lifetime' && request()->package_type != 'free'){
            $data['price'] = 'required';
        }
        if ($package->default === 'trial') {
            $data['no_of_days'] = 'sometimes|required|numeric|gt:0';
            $data['trial_message'] = 'sometimes|required';

            return $data;
        }

        if($package->default === 'yes'){
            return $data;
        }

        $data['description'] = 'required';

        if (request()->package_type == 'paid') {

            $gateways = GlobalPaymentGatewayCredentials::first();

            if ($this->has('monthly_status')) {

                $data['monthly_price'] = 'required|numeric|gt:0';

                if($gateways->razorpay_status == 'active'){
                    $data['razorpay_monthly_plan_id'] = 'required';
                }

                if($gateways->stripe_status == 'active'){
                    $data['stripe_monthly_plan_id'] = 'required';
                }
            }

            if ($this->has('annual_status')) {
                $data['annual_price'] = 'required|numeric|gt:0';

                if($gateways->razorpay_status == 'active'){
                    $data['razorpay_annual_plan_id'] = 'required';
                }

                if($gateways->stripe_status == 'active'){
                    $data['stripe_annual_plan_id'] = 'required';
                }
            }
        }

        return $data;
    }

}
