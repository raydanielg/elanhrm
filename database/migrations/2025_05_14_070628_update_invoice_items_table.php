<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Invoice;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {

            $invoiceIds = Invoice::where('company_id', $company->id)->pluck('id');

            if ($invoiceIds->isEmpty()) {
                continue;
            }

            $duplicates = DB::table('invoice_items')
            ->select('invoice_id', 'product_id', DB::raw('COUNT(*) as count'))
            ->whereIn('invoice_id', $invoiceIds)
            ->whereNotNull('product_id')
            ->groupBy('invoice_id', 'product_id')
            ->having('count', '>', 1)
            ->get();

            foreach ($duplicates as $dup) {

                $items = DB::table('invoice_items')
                    ->where('invoice_id', $dup->invoice_id)
                    ->where('product_id', $dup->product_id)
                    ->orderByDesc('id')
                    ->get();

                $totalQty = $items->sum('quantity');

                $keepRow = $items->first();
                $idsToDelete = $items->pluck('id')->slice(1); // All except the first

                $unitPrice = $keepRow->unit_price;
                $totalAmount = $totalQty * $unitPrice;

                // Delete duplicates
                DB::table('invoice_items')->whereIn('id', $idsToDelete)->delete();

                // Update quantity in the kept row
                DB::table('invoice_items')->where('id', $keepRow->id)->update(['quantity' => $totalQty, 'amount' => $totalAmount]);
            }

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
