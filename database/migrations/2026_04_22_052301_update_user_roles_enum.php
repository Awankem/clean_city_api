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
        // Handled in the original create_users_table migration for PostgreSQL compatibility
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Handled in the original create_users_table migration
    }
};
