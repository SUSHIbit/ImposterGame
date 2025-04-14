<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('round_id')->constrained()->onDelete('cascade');
            $table->foreignId('voter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('guessed_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['round_id', 'voter_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('votes');
    }
};