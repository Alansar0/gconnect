<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
    {
        public function up(): void
        {
        Schema::create('voucher_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')->constrained('vouchers')->onDelete('cascade');
            $table->foreignId('reseller_id')->constrained('resellers')->onDelete('cascade');
            $table->string('wan_port');
            $table->timestamp('expiry_time');
            $table->timestamps();
        });
    }


    public function down(): void
        {
            Schema::dropIfExists('voucher_queue');
        }
    };
