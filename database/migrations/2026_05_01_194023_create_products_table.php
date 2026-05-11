<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shop_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('car_make_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('car_model_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('part_type');
            $table->unsignedSmallInteger('year_from')->nullable();
            $table->unsignedSmallInteger('year_to')->nullable();
            $table->string('condition');
            $table->decimal('price', 12, 2);
            $table->string('currency')->default('KES');
            $table->string('location');
            $table->json('images')->nullable();
            $table->text('description')->nullable();
            $table->string('seller_name');
            $table->string('seller_phone');
            $table->string('seller_whatsapp')->nullable();
            $table->string('status')->default('active');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('sold_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
