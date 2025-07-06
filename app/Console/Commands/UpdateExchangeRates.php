<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\GlobalSetting;
use App\Models\Invoice;
use App\Models\Currency;
use App\Models\Payment;
use App\Models\Expense;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateExchangeRates extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-exchange-rate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates the exchange rates for all the currencies in currencies table.';


    /**
     * Execute the console command.
     *
     * @return mixed
     */

    public function handle()
    {
        $globalSetting = GlobalSetting::first();

        if (!$globalSetting) {
            return Command::SUCCESS;
        }

        $currencyApiKey = ($globalSetting->currency_converter_key) ?: config('app.currency_converter_key');

        if ($globalSetting->currency_key_version == 'dedicated') {
            $currencyApiKeyVersion = $globalSetting->dedicated_subdomain;
        } else {
            $currencyApiKeyVersion = $globalSetting->currency_key_version;
        }

        if ($currencyApiKey && $currencyApiKeyVersion) {

            $client = new Client();

            $this->info('Updating exchange rates...');

            $companiesCount = Company::count();
            $progressBar = $this->output->createProgressBar($companiesCount);
            $progressBar->start();

            // Cache exchange rates to avoid duplicate API calls
            $exchangeRateCache = [];

            try {
                $response = $client->request('GET', 'https://' . $currencyApiKeyVersion . '.currconv.com/api/v7/convert?q=USD_USD&compact=ultra&apiKey=' . $currencyApiKey);
                $response = json_decode($response->getBody(), true);
            } catch (Exception $e) {
                // Mark this conversion as failed in cache to avoid retrying
                $exchangeRateCache['USD_USD'] = false;
                // echo $e->getMessage();
                return Command::SUCCESS;
            }

            Company::with(['currencies', 'currency'])
                ->chunk(50, function ($companies) use ($currencyApiKey, $currencyApiKeyVersion, $client, $progressBar, &$exchangeRateCache) {
                    foreach ($companies as $company) {
                        $company->currencies->each(function ($currency) use ($currencyApiKey, $currencyApiKeyVersion, $company, $client, &$exchangeRateCache) {
                            $conversionKey = $currency->currency_code . '_' . $company->currency->currency_code;

                            // Skip if we've already had an API error for this conversion
                            if (isset($exchangeRateCache[$conversionKey]) && $exchangeRateCache[$conversionKey] === false) {
                                return;
                            }


                            try {
                                // Check if we already have this conversion rate cached
                                if (!isset($exchangeRateCache[$conversionKey])) {
                                    $response = $client->request('GET', 'https://' . $currencyApiKeyVersion . '.currconv.com/api/v7/convert?q=' . $conversionKey . '&compact=ultra&apiKey=' . $currencyApiKey);
                                    $response = json_decode($response->getBody(), true);
                                    $exchangeRateCache[$conversionKey] = $response[$conversionKey];
                                }

                                $currency->exchange_rate = $exchangeRateCache[$conversionKey];
                                $currency->saveQuietly();
                            } catch (Exception $e) {
                                // Mark this conversion as failed in cache to avoid retrying
                                $exchangeRateCache[$conversionKey] = false;
                                // echo $e->getMessage();
                            }
                        });
                        $progressBar->advance();
                    }
                });

            $progressBar->finish();
            $this->newLine();

            $this->info('Updating invoices...');
            $this->invoices();

            $this->info('Updating payments...');
            $this->payments();

            $this->info('Updating expenses...');
            $this->expenses();

            $this->info('All updates completed successfully!');

            return Command::SUCCESS;
        }

        return Command::SUCCESS;
    }

    private function invoices()
    {
        $invoices = Invoice::all();
        $progressBar = $this->output->createProgressBar(count($invoices));
        $progressBar->start();

        foreach ($invoices as $invoice) {
            $currency = Currency::where('id', $invoice->currency_id)->first();

            if ($currency) {
                $invoice->exchange_rate = $currency->exchange_rate;
                $invoice->save();
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    private function payments()
    {
        $payments = Payment::all();
        $progressBar = $this->output->createProgressBar(count($payments));
        $progressBar->start();

        foreach ($payments as $payment) {
            $currency = Currency::where('id', $payment->currency_id)->first();

            if ($currency) {
                $payment->exchange_rate = $currency->exchange_rate;
                $payment->saveQuietly();
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }

    private function expenses()
    {
        $expenses = Expense::all();
        $progressBar = $this->output->createProgressBar(count($expenses));
        $progressBar->start();

        foreach ($expenses as $expense) {
            $currency = Currency::where('id', $expense->currency_id)->first();

            if ($currency) {
                $expense->exchange_rate = $currency->exchange_rate;
                $expense->save();
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
    }
}
