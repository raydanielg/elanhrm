<?php

namespace  App\Http\Requests\SuperAdmin\GlobalCurrency;;

use App\Http\Requests\CoreRequest;

class StoreGlobalCurrencyExchangeKey extends CoreRequest
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
            'currency_converter_key' => 'required',
        ];
    }

}
