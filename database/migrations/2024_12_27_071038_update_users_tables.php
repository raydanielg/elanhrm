<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Company;
use App\Models\User;
use App\Models\ClientContact;
use App\Scopes\ActiveScope;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {

            $clients = User::withoutGlobalScope(ActiveScope::class)->where('company_id', $company->id)
                ->where('status', 'deactive')
                ->whereNull('is_client_contact')
                ->get();

            $clientIds = [];

            foreach ($clients as $client) {

                $clientContacts = ClientContact::where('user_id', $client->id)->get();

                foreach ($clientContacts as $contact) {
                    $clientIds[] = $contact->client_id;
                }
            }

            if (!empty($clientIds)) {
                User::withoutGlobalScope(ActiveScope::class)->whereIn('id', $clientIds)->where('status', 'active')->update(['status' => 'deactive']);
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
