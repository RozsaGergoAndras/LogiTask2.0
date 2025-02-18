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
        Schema::create('api_access_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('ip_address');
            $table->string('user_agent');
            $table->string('route');
            $table->string('method');
            $table->string('request_data');
            $table->unsignedBigInteger('user_id');
            //Kapcsolatok
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_access_logs');
    }
};
