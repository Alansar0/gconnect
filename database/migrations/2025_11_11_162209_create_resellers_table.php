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
            Schema::create('resellers', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // owner
                    $table->string('name')->unique(); // visible hotspot name
                    $table->decimal('commission_percent', 5, 2)->default(0);
                    $table->enum('status', ['pending','active','suspended'])->default('pending');
                    $table->timestamps();
            });

        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resellers');
    }
};
