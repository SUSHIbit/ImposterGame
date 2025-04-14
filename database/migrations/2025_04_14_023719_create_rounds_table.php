<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('round_number');
            $table->foreignId('question_set_id')->constrained()->onDelete('cascade');
            $table->foreignId('imposter_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'answering', 'voting', 'completed'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['room_id', 'round_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rounds');
    }
};