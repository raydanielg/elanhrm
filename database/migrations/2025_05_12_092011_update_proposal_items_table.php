<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Company;
use App\Models\Proposal;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {

            $proposalIds = Proposal::where('company_id', $company->id)->pluck('id');

            if ($proposalIds->isEmpty()) {
                continue;
            }

            $duplicates = DB::table('proposal_items')
            ->select('proposal_id', 'product_id', DB::raw('COUNT(*) as count'))
            ->whereIn('proposal_id', $proposalIds)
            ->whereNotNull('product_id')
            ->groupBy('proposal_id', 'product_id')
            ->having('count', '>', 1)
            ->get();

            foreach ($duplicates as $dup) {

                $items = DB::table('proposal_items')
                    ->where('proposal_id', $dup->proposal_id)
                    ->where('product_id', $dup->product_id)
                    ->orderByDesc('id')
                    ->get();

                $totalQty = $items->sum('quantity');

                $keepRow = $items->first();
                $idsToDelete = $items->pluck('id')->slice(1); // All except the first

                $unitPrice = $keepRow->unit_price;
                $totalAmount = $totalQty * $unitPrice;

                // Delete duplicates
                DB::table('proposal_items')->whereIn('id', $idsToDelete)->delete();

                // Update quantity in the kept row
                DB::table('proposal_items')->where('id', $keepRow->id)->update(['quantity' => $totalQty, 'amount' => $totalAmount]);
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
