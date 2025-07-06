<?php

namespace App\Http\Requests\SuperAdmin\Packages;

use App\Models\SuperAdmin\GlobalPaymentGatewayCredentials;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $data = [
            'currency_id' => 'required|exists:global_currencies,id',
            'name' => [
                'required',
                Rule::unique('packages')->where(function ($query) {
                    return $query->where('currency_id', $this->get('currency_id'));
                }),
            ],
            'description' => 'required',
            'max_employees' => 'required|numeric',
            'max_storage_size' => 'required|gte:-1',
            'storage_unit' => 'required|in:gb,mb',
        ];

        $gateways = GlobalPaymentGatewayCredentials::first();

        if(request()->package == 'lifetime' && request()->package_type != 'free'){
            $data['price'] = 'required';
        }
        if(request()->package_type == 'paid' && $this->has('monthly_status') && request()->package != 'lifetime'){
            $data['monthly_price'] = 'required|numeric|gt:0';


            if(($this->get('annual_price') > 0 && $this->get('monthly_price') > 0 ) && $gateways->razorpay_status == 'active'){
                $data['razorpay_annual_plan_id'] = 'required';
                $data['razorpay_monthly_plan_id'] = 'required';
            }
        }

        if(request()->package_type == 'paid' && $this->has('annual_status')  && request()->package != 'lifetime'){
            $data['annual_price'] = 'required|numeric|gt:0';

            if($this->get('annual_price') > 0 && $this->get('monthly_price') > 0 && $gateways->stripe_status == 'active'){
                $data['stripe_annual_plan_id'] = 'required';
                $data['stripe_monthly_plan_id'] = 'required';
            }
        }

        return $data;
    }

}
