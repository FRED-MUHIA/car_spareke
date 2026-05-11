<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('account_code')->nullable()->unique()->after('id');
            $table->foreignId('pricing_plan_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });

        User::query()->whereNull('account_code')->lazyById()->each(function (User $user): void {
            $user->forceFill(['account_code' => User::generateAccountCode()])->save();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pricing_plan_id');
            $table->dropUnique(['account_code']);
            $table->dropColumn('account_code');
        });
    }
};
