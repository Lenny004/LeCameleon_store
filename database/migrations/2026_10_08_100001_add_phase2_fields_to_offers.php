<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offers', function (Blueprint $table): void {
            $table->decimal('accepted_amount', 12, 2)->nullable()->after('counter_amount');
            $table->timestamp('responded_at')->nullable()->after('expires_at');
            $table->foreignUuid('order_id')->nullable()->after('responded_at')->constrained('orders')->nullOnDelete();
            $table->timestamp('expiry_notified_at')->nullable()->after('order_id');

            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::table('offers', function (Blueprint $table): void {
            $table->dropForeign(['order_id']);
            $table->dropIndex(['status', 'expires_at']);
            $table->dropColumn(['accepted_amount', 'responded_at', 'order_id', 'expiry_notified_at']);
        });
    }
};
