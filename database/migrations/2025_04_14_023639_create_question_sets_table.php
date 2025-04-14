<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('question_sets', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('set_number')->unique();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('question_sets');
    }
};
