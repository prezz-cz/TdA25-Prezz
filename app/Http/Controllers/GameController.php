<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function getAll()
    {
        $games = Game::all();
        return view('games', compact('games'));
    }

    public function new() {}

    public function get($uuid)
    {
        $game = Game::findOrFail($uuid);
        return view('game', compact('game'));
    }

    public function update($uuid, $newGame)
    {
        $game = Game::findOrFail($uuid);
        $game->board->update($newGame);
    }

    public function remove($uuid)
    {
        $game = Game::findOrFail($uuid);
        $game->delete();
        return redirect('/game');
    }
}
