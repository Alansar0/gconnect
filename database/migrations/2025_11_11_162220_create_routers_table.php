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
            Schema::create('routers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reseller_id')->constrained()->onDelete('cascade');
                $table->string('host')->nullable(); // WireGuard IP or hostname
                $table->integer('port')->default(8728);
                $table->string('username')->nullable();
                $table->text('password')->nullable(); // encrypted password blob
                $table->string('notes')->nullable();
                $table->boolean('active')->default(false);
                $table->boolean('is_online')->default(false);
                $table->timestamp('last_seen_at')->nullable();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routers');
    }
};
