<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
{
    Schema::create('transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('type');
        $table->decimal('amount', 16, 2);
        $table->string('status')->default('pending');
        $table->string('reference')->unique();
        $table->text('description')->nullable();
        $table->decimal('commission_percent', 5, 2)->nullable();
        $table->decimal('prev_balance', 16, 2)->nullable();
        $table->decimal('new_balance', 16, 2)->nullable();
        $table->string('gateway')->nullable(); // e.g. paymentpoint
        $table->timestamps();
    });
}


    public function down(): void {
        Schema::dropIfExists('transactions');
    }
};

