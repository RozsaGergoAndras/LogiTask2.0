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
        Schema::create('task_contents', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('link');
            $table->unsignedBigInteger('task_id');
            $table->softDeletes();
            //Kapcsolatok
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_contents');
    }
};
