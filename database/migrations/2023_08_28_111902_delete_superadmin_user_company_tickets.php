<?php

use App\Models\Ticket;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{

    public function up(): void
    {
        Ticket::withoutGlobalScopes()->withTrashed()->whereHas('requester', function ($q) {
            $q->withoutGlobalScopes()->where('is_superadmin', 1);
        })->forceDelete();
    }

};
