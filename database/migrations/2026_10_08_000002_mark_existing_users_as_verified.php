<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('UPDATE users SET email_verified_at = COALESCE(email_verified_at, created_at, CURRENT_TIMESTAMP)');
    }

    public function down(): void
    {
        // Existing verification timestamps must not be removed on rollback.
    }
};
