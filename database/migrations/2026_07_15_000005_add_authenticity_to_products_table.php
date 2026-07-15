<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_authenticated')->default(false)->after('measurements');
            $table->text('authenticity_notes')->nullable()->after('is_authenticated');
            $table->timestamp('authenticated_at')->nullable()->after('authenticity_notes');
            $table->foreignUuid('authenticated_by')->nullable()->after('authenticated_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('authenticated_by');
            $table->dropColumn(['is_authenticated', 'authenticity_notes', 'authenticated_at']);
        });
    }
};
