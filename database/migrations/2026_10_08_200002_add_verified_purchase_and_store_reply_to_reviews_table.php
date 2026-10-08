<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table): void {
            $table->boolean('is_verified_purchase')->default(false)->after('is_approved');
            $table->text('store_reply')->nullable()->after('is_verified_purchase');
            $table->timestamp('store_replied_at')->nullable()->after('store_reply');
            $table->foreignUuid('store_replied_by')->nullable()->after('store_replied_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table): void {
            $table->dropForeign(['store_replied_by']);
            $table->dropColumn([
                'is_verified_purchase',
                'store_reply',
                'store_replied_at',
                'store_replied_by',
            ]);
        });
    }
};
