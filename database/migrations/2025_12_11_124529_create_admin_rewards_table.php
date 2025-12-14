<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('for')->unique(); // 'karanta', 'sauraro', 'morning', 'evening'
            $table->decimal('cashback_amount', 10, 2)->default(50); // default value
            $table->decimal('voucher_rate', 10, 2)->default(200); // how much cashback equals 1 voucher
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // Insert defaults
        DB::table('admin_rewards')->insert([
            ['for' => 'karanta', 'cashback_amount' => 50, 'voucher_rate' => 200, 'created_at' => now(), 'updated_at' => now()],
            ['for' => 'sauraro', 'cashback_amount' => 50, 'voucher_rate' => 200, 'created_at' => now(), 'updated_at' => now()],
            ['for' => 'morning', 'cashback_amount' => 50, 'voucher_rate' => 200, 'created_at' => now(), 'updated_at' => now()],
            ['for' => 'evening', 'cashback_amount' => 50, 'voucher_rate' => 200, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_rewards');
    }
};
