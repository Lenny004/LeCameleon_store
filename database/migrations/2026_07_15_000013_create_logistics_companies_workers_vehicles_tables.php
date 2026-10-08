<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_companies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 150);
            $table->string('legal_name', 200)->nullable();
            $table->string('trade_name', 150)->nullable();
            $table->string('tax_id', 30)->nullable()->comment('NIT');
            $table->string('email', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('address_line', 255)->nullable();
            $table->foreignId('sv_municipality_id')->nullable()->constrained('sv_municipalities')->nullOnDelete();
            $table->string('website', 255)->nullable();
            $table->string('logo_path', 500)->nullable();
            $table->string('contact_person', 150)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('logistics_workers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('logistics_company_id')->nullable()->constrained('logistics_companies')->nullOnDelete();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employee_code', 30)->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('document_id', 20)->nullable()->comment('DUI');
            $table->string('phone', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('role', 20)->comment('collector|driver|dispatcher|supervisor');
            $table->date('hire_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('role');
        });

        Schema::create('logistics_vehicles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('logistics_company_id')->constrained('logistics_companies')->cascadeOnDelete();
            $table->foreignUuid('logistics_worker_id')->nullable()->constrained('logistics_workers')->nullOnDelete();
            $table->string('plate_number', 20)->unique();
            $table->string('brand', 80)->nullable();
            $table->string('model', 80)->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->string('color', 40)->nullable();
            $table->string('vehicle_type', 20)->comment('motorcycle|van|truck|bicycle|car');
            $table->decimal('capacity_kg', 8, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('vehicle_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_vehicles');
        Schema::dropIfExists('logistics_workers');
        Schema::dropIfExists('logistics_companies');
    }
};
