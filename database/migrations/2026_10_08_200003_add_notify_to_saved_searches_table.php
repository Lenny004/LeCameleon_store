<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saved_searches', function (Blueprint $table): void {
            $table->boolean('notify')->default(true)->after('query_params');
        });
    }

    public function down(): void
    {
        Schema::table('saved_searches', function (Blueprint $table): void {
            $table->dropColumn('notify');
        });
    }
};
