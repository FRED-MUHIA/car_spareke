<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('garages', function (Blueprint $table) {
            $table->timestamp('public_verified_at')->nullable()->after('license_path');
        });
    }

    public function down(): void
    {
        Schema::table('garages', function (Blueprint $table) {
            $table->dropColumn('public_verified_at');
        });
    }
};
