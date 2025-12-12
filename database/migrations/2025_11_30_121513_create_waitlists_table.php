<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('waitlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('profile_id')->nullable()->constrained('voucher_profiles')->nullOnDelete();
            $table->integer('position')->nullable();
            $table->timestamp('expected_available_at')->nullable();
            $table->enum('status', ['waiting','notified','skipped','fulfilled','cancelled'])->default('waiting');
            $table->timestamp('notified_at')->nullable();
            $table->index(['reseller_id', 'status']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlists');
    }
};
