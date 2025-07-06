<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Estimate;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {

            $estimateIds = Estimate::where('company_id', $company->id)->pluck('id');

            if ($estimateIds->isEmpty()) {
                continue;
            }

            $duplicates = DB::table('estimate_items')
            ->select('estimate_id', 'product_id', DB::raw('COUNT(*) as count'))
            ->whereIn('estimate_id', $estimateIds)
            ->whereNotNull('product_id')
            ->groupBy('estimate_id', 'product_id')
            ->having('count', '>', 1)
            ->get();

            foreach ($duplicates as $dup) {

                $items = DB::table('estimate_items')
                    ->where('estimate_id', $dup->estimate_id)
                    ->where('product_id', $dup->product_id)
                    ->orderByDesc('id')
                    ->get();

                $totalQty = $items->sum('quantity');

                $keepRow = $items->first();
                $idsToDelete = $items->pluck('id')->slice(1); // All except the first

                $unitPrice = $keepRow->unit_price;
                $totalAmount = $totalQty * $unitPrice;

                // Delete duplicates
                DB::table('estimate_items')->whereIn('id', $idsToDelete)->delete();

                // Update quantity in the kept row
                DB::table('estimate_items')->where('id', $keepRow->id)->update(['quantity' => $totalQty, 'amount' => $totalAmount]);
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
