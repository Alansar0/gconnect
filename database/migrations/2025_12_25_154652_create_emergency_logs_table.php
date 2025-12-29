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
       Schema::create('emergency_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('activated_by')->constrained('users')->onDelete('cascade');
        $table->string('status');
        $table->text('note')->nullable();
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emergency_logs');
    }
};
