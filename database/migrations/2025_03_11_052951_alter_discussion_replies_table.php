<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('discussion_replies', function (Blueprint $table) {
            $table->unsignedInteger('added_by')->nullable()->index('discussion_replies_added_by_foreign');
            $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
        });

        Schema::table('pinned', function (Blueprint $table) {
            $table->unsignedInteger('added_by')->nullable()->index('pinned_added_by_foreign');
            $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
        });

        Schema::table('ticket_replies', function (Blueprint $table) {
            $table->unsignedInteger('added_by')->nullable()->index('ticket_replies_added_by_foreign');
            $table->foreign(['added_by'])->references(['id'])->on('users')->onUpdate('CASCADE')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discussion_replies', function (Blueprint $table) {
            $table->dropColumn('added_by');
        });

        Schema::table('pinned', function (Blueprint $table) {
            $table->dropColumn('added_by');
        });

        Schema::table('ticket_replies', function (Blueprint $table) {
            $table->dropColumn('added_by');
        });
    }
};
