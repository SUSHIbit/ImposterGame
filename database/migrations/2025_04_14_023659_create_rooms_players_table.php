<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('room_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('is_ready')->default(false);
            $table->unsignedInteger('score')->default(0);
            $table->timestamps();
            
            $table->unique(['room_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('room_players');
    }
};