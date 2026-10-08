<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table): void {
            $table->foreignId('sv_municipality_id')->nullable()->after('country')->constrained('sv_municipalities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table): void {
            $table->dropForeign(['sv_municipality_id']);
            $table->dropColumn('sv_municipality_id');
        });
    }
};
