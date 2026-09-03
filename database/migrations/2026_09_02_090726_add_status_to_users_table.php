<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // pending = email verified, awaiting admin approval
            // approved = admin approved, welcome email sent
            // rejected = admin rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('is_admin');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
