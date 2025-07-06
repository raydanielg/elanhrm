<?php

use App\Models\Company;
use App\Models\Currency;
use App\Models\CurrencyFormatSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        if (!Schema::hasColumn('currencies', 'no_of_decimal')) {
            Schema::table('currencies', function (Blueprint $table) {
                $table->unsignedInteger('no_of_decimal')->default(2);
                $table->string('thousand_separator')->nullable();
                $table->string('decimal_separator')->nullable();

                if (Schema::hasColumn('currencies', 'currency_position')) {
                    DB::statement("ALTER TABLE currencies CHANGE COLUMN currency_position currency_position ENUM('left', 'right', 'left_with_space', 'right_with_space') NOT NULL DEFAULT 'left'");
                }
                else {
                    $table->enum('currency_position', ['left', 'right', 'left_with_space', 'right_with_space'])->default('left');
                }
            });


            $companies = Company::select('id')->get();

            foreach ($companies as $company) {
                $currencyFormat = CurrencyFormatSetting::where('company_id', $company->id)->first();

                Currency::where('company_id', $company->id)
                    ->update([
                        'currency_position' => $currencyFormat->currency_position,
                        'no_of_decimal' => $currencyFormat->no_of_decimal,
                        'thousand_separator' => $currencyFormat->thousand_separator,
                        'decimal_separator' => $currencyFormat->decimal_separator
                    ]);
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn('currency_position');
            $table->dropColumn('no_of_decimal');
            $table->dropColumn('thousand_separator');
            $table->dropColumn('decimal_separator');
        });


    }

};
