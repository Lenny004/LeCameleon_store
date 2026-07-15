<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('related_product_id')->constrained('products')->cascadeOnDelete();
            $table->string('type', 20);
            $table->timestamps();

            $table->unique(['product_id', 'related_product_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_relations');
    }
};
