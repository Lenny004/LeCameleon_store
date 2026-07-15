<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sv_departments', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique()->comment('ISO-style department code, e.g. SS, LI');
            $table->string('name', 120);
            $table->string('slug', 140)->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sv_municipalities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sv_department_id')->constrained('sv_departments')->cascadeOnDelete();
            $table->string('code', 20)->unique()->comment('Unique municipality code, e.g. SS-C');
            $table->string('name', 150);
            $table->string('slug', 180)->unique();
            $table->string('region_label', 20)->comment('Norte|Sur|Este|Oeste|Centro|Costa');
            $table->decimal('base_shipping_cost', 10, 2)->default(0);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('sv_department_id');
            $table->index('region_label');
        });

        Schema::create('sv_districts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sv_municipality_id')->constrained('sv_municipalities')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('slug', 180);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['sv_municipality_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sv_districts');
        Schema::dropIfExists('sv_municipalities');
        Schema::dropIfExists('sv_departments');
    }
};
