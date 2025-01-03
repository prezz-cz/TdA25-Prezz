<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGamesTable extends Migration
{
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('name');
            $table->enum('difficulty', ['beginner', 'easy', 'medium', 'hard', 'extreme']);
            $table->enum('game_state', ['opening', 'midgame', 'endgame', 'unknown'])->default('unknown');
            $table->json('board');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('games');
    }
}
