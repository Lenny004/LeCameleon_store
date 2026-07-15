<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignUuid('logistics_company_id')->nullable()->after('order_id')->constrained('logistics_companies')->nullOnDelete();
            $table->foreignUuid('logistics_worker_id')->nullable()->after('logistics_company_id')->constrained('logistics_workers')->nullOnDelete();
            $table->foreignUuid('logistics_vehicle_id')->nullable()->after('logistics_worker_id')->constrained('logistics_vehicles')->nullOnDelete();
            $table->foreignId('origin_municipality_id')->nullable()->after('logistics_vehicle_id')->constrained('sv_municipalities')->nullOnDelete();
            $table->foreignId('destination_municipality_id')->nullable()->after('origin_municipality_id')->constrained('sv_municipalities')->nullOnDelete();
            $table->foreignId('shipping_zone_rate_id')->nullable()->after('destination_municipality_id')->constrained('shipping_zone_rates')->nullOnDelete();
            $table->decimal('quoted_fee', 10, 2)->nullable()->after('shipping_zone_rate_id');
            $table->decimal('distance_km', 8, 2)->nullable()->after('quoted_fee');
            $table->string('recipient_name', 150)->nullable()->after('distance_km');
            $table->string('recipient_phone', 30)->nullable()->after('recipient_name');
            $table->text('recipient_notes')->nullable()->after('recipient_phone');
            $table->timestamp('next_attempt_at')->nullable()->after('delivered_at');
        });

        // Widen status column for expanded logistics lifecycle values.
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('status', 30)->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->change();
        });

        Schema::table('shipments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('logistics_company_id');
            $table->dropConstrainedForeignId('logistics_worker_id');
            $table->dropConstrainedForeignId('logistics_vehicle_id');
            $table->dropConstrainedForeignId('origin_municipality_id');
            $table->dropConstrainedForeignId('destination_municipality_id');
            $table->dropConstrainedForeignId('shipping_zone_rate_id');
            $table->dropColumn([
                'quoted_fee',
                'distance_km',
                'recipient_name',
                'recipient_phone',
                'recipient_notes',
                'next_attempt_at',
            ]);
        });
    }
};
