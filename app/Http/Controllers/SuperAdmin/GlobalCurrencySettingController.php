<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Helper\Reply;
use App\Http\Controllers\AccountBaseController;
use App\Http\Requests\SuperAdmin\GlobalCurrency\StoreGlobalCurrencyExchangeKey;
use App\Http\Requests\SuperAdmin\GlobalCurrency\StoreGlobalCurrency;
use App\Http\Requests\SuperAdmin\GlobalCurrency\UpdateGlobalCurrency;
use App\Models\GlobalSetting;
use App\Models\SuperAdmin\GlobalCurrency;
use GuzzleHttp\Client;
use App\Traits\SuperAdmin\GlobalCurrencyExchange;

class GlobalCurrencySettingController extends AccountBaseController
{

    use GlobalCurrencyExchange;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.currencySettings';
        $this->activeSettingMenu = 'currency_settings';
        $this->middleware(function ($request, $next) {
            abort_403(user()->permission('manage_currency_setting') !== 'all' && GlobalSetting::validateSuperAdmin('manage_superadmin_currency_settings'));

            return $next($request);
        });
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|mixed
     */
    public function index()
    {
        $this->currencies = GlobalCurrency::all();
        $this->defaultFormattedCurrency = global_currency_format('1234567.89');

        $this->view = 'super-admin.currency-settings.ajax.currency-setting';

        $this->activeTab = 'currency-setting';

        if (request()->ajax()) {
            $html = view($this->view, $this->data)->render();

            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle, 'activeTab' => $this->activeTab]);
        }

        return view('super-admin.currency-settings.index', $this->data);

    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $this->currencies = GlobalCurrency::all();
        $this->currencyFormatSetting = global_currency_format_setting();
        $this->defaultFormattedCurrency = global_currency_format('1234567.89');

        return view('super-admin.currency-settings.create', $this->data);
    }

    /**
     * @param StoreGlobalCurrency $request
     * @return array|string[]
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */
    public function store(StoreGlobalCurrency $request)
    {
        $currency = new GlobalCurrency();
        $currency->currency_name = $request->currency_name;
        $currency->currency_symbol = $request->currency_symbol;
        $currency->currency_code = $request->currency_code;
        $currency->is_cryptocurrency = $request->is_cryptocurrency;
        $currency->exchange_rate = $request->exchange_rate;
        $currency->usd_price = $request->usd_price;
        $currency->currency_position = $request->currency_position;
        $currency->no_of_decimal = $request->no_of_decimal ?? 2;
        $currency->thousand_separator = $request->thousand_separator;
        $currency->decimal_separator = $request->decimal_separator;
        $currency->save();

        $this->updateExchangeRates();

        return Reply::success(__('messages.recordSaved'));
    }

    public function show($id)
    {
        return redirect(route('currency-settings.edit', $id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $this->currency = GlobalCurrency::findOrFail($id);
        $this->defaultFormattedCurrency = global_currency_format('1234567.89', $id);

        return view('super-admin.currency-settings.edit', $this->data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return array|string[]
     */
    public function update(UpdateGlobalCurrency $request, $id)
    {
        $currency = GlobalCurrency::findOrFail($id);
        $currency->currency_name = $request->currency_name;
        $currency->currency_symbol = $request->currency_symbol;
        $currency->currency_code = $request->currency_code;
        $currency->is_cryptocurrency = $request->is_cryptocurrency;
        $currency->exchange_rate = $request->exchange_rate;
        $currency->usd_price = $request->usd_price;
        $currency->currency_position = $request->currency_position;
        $currency->no_of_decimal = $request->no_of_decimal;
        $currency->thousand_separator = $request->thousand_separator;
        $currency->decimal_separator = $request->decimal_separator;
        $currency->save();

        cache()->forget('global_currency_format_setting' . $currency->id);

        return Reply::success(__('messages.updateSuccess'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return array
     */
    public function destroy($id)
    {
        if ($this->company->currency_id == $id) {
            return Reply::error(__('modules.currencySettings.cantDeleteDefault'));
        }

        GlobalCurrency::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function exchangeRate($currency)
    {
        $currencyApiKey = ($this->global->currency_converter_key) ?: config('app.currency_converter_key');
        $currencyApiKeyVersion = $this->global->currency_key_version;

        try {
            // Get exchange rate
            $client = new Client();
            $res = $client->request('GET', 'https://' . $currencyApiKeyVersion . '.currconv.com/api/v7/convert?q=' . $this->global->currency->currency_code . '_' . $currency . '&compact=ultra&apiKey=' . $currencyApiKey);
            $conversionRate = $res->getBody();
            $conversionRate = json_decode($conversionRate, true);
            $rate = $conversionRate[mb_strtoupper($this->global->currency->currency_code) . '_' . $currency];

            return Reply::dataOnly(['status' => 'success', 'value' => $rate]);

        } catch (\Throwable $th) {
            return Reply::error(__('messages.errorOccured'));
        }
    }

    /**
     * @return array
     */
    public function updateExchangeRate()
    {
        $currencyApiKey = ($this->global->currency_converter_key) ?: config('app.currency_converter_key');

        if (is_null($currencyApiKey)) {
            return Reply::error(__('messages.currencyExchangeKeyNotFound'));
        }

        $this->updateExchangeRates();

        return Reply::success(__('messages.updateSuccess'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function currencyExchangeKey()
    {
        return view('super-admin.currency-settings.currency-exchange-modal', $this->data);
    }

    /**
     * @param StoreGlobalCurrencyExchangeKey $request
     * @return array
     */
    public function currencyExchangeKeyStore(StoreGlobalCurrencyExchangeKey $request)
    {
        $this->global->currency_converter_key = $request->currency_converter_key;
        $this->global->currency_key_version = $request->currency_key_version;

        if($request->currency_key_version == 'dedicated'){
            $this->global->dedicated_subdomain = $request->dedicated_subdomain;
        }else{
            $this->global->dedicated_subdomain = null;
        }

        $this->global->save();

        // remove cache
        cache()->forget('global_setting');


        return Reply::success(__('messages.currencyConvertKeyUpdated'));
    }

}
