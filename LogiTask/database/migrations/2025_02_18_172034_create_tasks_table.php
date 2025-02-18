<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\SoftDeletes;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('assigner');
            $table->unsignedBigInteger('worker');
            $table->integer('state');
            $table->timestamp('state0date')->nullable();
            $table->timestamp('state1date')->nullable();
            $table->timestamp('state2date')->nullable();
            $table->unsignedBigInteger('task_type');
            $table->string('description');
            $table->SoftDeletes();
            //Kapcsolatok
            $table->foreign('assigner')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('worker')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('task_type')->references('id')->on('task_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
