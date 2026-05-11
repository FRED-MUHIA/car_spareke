<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('garages', function (Blueprint $table) {
            $table->json('specialization_brands')->nullable()->after('services');
        });
    }

    public function down(): void
    {
        Schema::table('garages', function (Blueprint $table) {
            $table->dropColumn('specialization_brands');
        });
    }
};
