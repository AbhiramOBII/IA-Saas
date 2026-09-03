<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('token_mac_bindings', function (Blueprint $table) {
            $table->id();
            // Passport token ID (the JWT jti claim) — UUID string
            $table->string('token_id', 100)->unique()->index();
            // Normalised MAC address e.g. aa:bb:cc:dd:ee:ff
            $table->string('mac_address', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_mac_bindings');
    }
};
