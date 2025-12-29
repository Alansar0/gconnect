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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained()->onDelete('cascade');
            $table->string('code');
            $table->string('password');
            $table->string('profile_id');
            $table->enum('status', ['active', 'used', 'expired'])->default('active');
            $table->timestamps();
            $table->boolean('reminded_90')->default(false);
            $table->boolean('reminded_40')->default(false);
            $table->boolean('reminded_20')->default(false);
            $table->timestamp('expires_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
