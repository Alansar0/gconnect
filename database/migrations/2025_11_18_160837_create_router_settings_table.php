<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        Schema::create('router_settings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reseller_id')->constrained()->onDelete('cascade');
                // $table->string('active_wan_port')->default('ether1');
                $table->integer('wan1_limit')->default(0);
                $table->integer('wan2_limit')->default(0);
                $table->integer('wan1_current_count')->default(0);
                $table->integer('wan2_current_count')->default(0);
                $table->timestamp('global_sold_out_until')->nullable();
                $table->timestamps();
        });
    }


    public function down(): void
        {
            Schema::dropIfExists('router_settings');
        }
    };
