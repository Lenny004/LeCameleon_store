<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->foreignId('sv_municipality_id')->nullable()->constrained('sv_municipalities')->nullOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('shipping_zone_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('origin_zone_id')->constrained('shipping_zones')->cascadeOnDelete();
            $table->foreignId('destination_zone_id')->constrained('shipping_zones')->cascadeOnDelete();
            $table->decimal('base_fee', 10, 2);
            $table->decimal('per_km_fee', 10, 2)->default(0);
            $table->decimal('min_fee', 10, 2)->default(0);
            $table->decimal('max_fee', 10, 2)->nullable();
            $table->decimal('estimated_hours', 6, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['origin_zone_id', 'destination_zone_id']);
        });

        Schema::create('dispatch_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->timestamp('next_dispatch_at');
            $table->timestamp('cutoff_at');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_warnings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('title', 200);
            $table->text('body');
            $table->string('severity', 20)->comment('info|warning|danger');
            $table->string('applies_to', 20)->comment('checkout|tracking|admin');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_warnings');
        Schema::dropIfExists('dispatch_schedules');
        Schema::dropIfExists('shipping_zone_rates');
        Schema::dropIfExists('shipping_zones');
    }
};
