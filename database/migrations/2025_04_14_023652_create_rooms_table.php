<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('code', 5)->unique();
            $table->foreignId('host_user_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['waiting', 'in_progress', 'completed'])->default('waiting');
            $table->unsignedInteger('min_players')->default(4);
            $table->unsignedInteger('max_players')->default(10);
            $table->unsignedInteger('current_round')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rooms');
    }
};