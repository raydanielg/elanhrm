<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Order;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {

            $orderIds = Order::where('company_id', $company->id)->pluck('id');

            if ($orderIds->isEmpty()) {
                continue;
            }

            $duplicates = DB::table('order_items')
            ->select('order_id', 'product_id', DB::raw('COUNT(*) as count'))
            ->whereIn('order_id', $orderIds)
            ->whereNotNull('product_id')
            ->groupBy('order_id', 'product_id')
            ->having('count', '>', 1)
            ->get();

            foreach ($duplicates as $dup) {

                $items = DB::table('order_items')
                    ->where('order_id', $dup->order_id)
                    ->where('product_id', $dup->product_id)
                    ->orderByDesc('id')
                    ->get();

                $totalQty = $items->sum('quantity');

                $keepRow = $items->first();
                $idsToDelete = $items->pluck('id')->slice(1); // All except the first

                $unitPrice = $keepRow->unit_price;
                $totalAmount = $totalQty * $unitPrice;

                // Delete duplicates
                DB::table('order_items')->whereIn('id', $idsToDelete)->delete();

                // Update quantity in the kept row
                DB::table('order_items')->where('id', $keepRow->id)->update(['quantity' => $totalQty, 'amount' => $totalAmount]);
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