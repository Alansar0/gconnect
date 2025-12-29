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
        Schema::create('waitlist_daily_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reseller_id');

            $table->date('snapshot_date'); // yesterday
            $table->integer('waiting_count');

            // Optional intelligence
            $table->integer('waiting_24h_plus')->default(0);
            $table->integer('waiting_48h_plus')->default(0);
            $table->integer('waiting_72h_plus')->default(0);

            $table->timestamps();

            $table->unique(['reseller_id', 'snapshot_date']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waitlist_daily_stats');
    }
};
