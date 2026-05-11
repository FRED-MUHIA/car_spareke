<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pricing_plan_id')->constrained()->cascadeOnDelete();
            $table->string('account_code');
            $table->decimal('amount', 10, 2);
            $table->string('phone');
            $table->string('status')->default('pending');
            $table->string('merchant_request_id')->nullable();
            $table->string('checkout_request_id')->nullable();
            $table->string('response_code')->nullable();
            $table->string('response_description')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_payments');
    }
};
