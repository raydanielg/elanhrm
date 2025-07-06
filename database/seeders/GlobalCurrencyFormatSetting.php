<?php

namespace Database\Seeders;

use App\Models\CurrencyFormatSetting;
use Illuminate\Database\Seeder;

class GlobalCurrencyFormatSetting extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $format = new CurrencyFormatSetting();
        $format->currency_position = 'left';
        $format->no_of_decimal = 2;
        $format->thousand_separator = ',';
        $format->decimal_separator = '.';
        $format->save();
    }

}
