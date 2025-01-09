<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Requests\GameRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class GameController extends Controller
{

    //*  200 = Vsechny hry
    public function getAll()
    {
        try {
            $response = Http::get('http://127.0.0.1:8000/api/v1/games');
            dd($response->status(), $response->body());
        } catch (\Exception $e) {
            dd($e->getMessage());
        }        
        if ($response->ok()) {
            $games = $response->json(); 
        } else {
            $games = [];
        }

        return view('components/schemas/Games', compact('games'));
    }

    //*  {
    //*      "name": "Moje první hra",
    //*      "difficulty": "hard",
    //*      "board": "[15x[15x'']"
    //*  }
    //*  201 = Hra úspěšně vytvořena.
    //!  400 = Bad request: ${reason}
    //!  422 = Semantic error: ${reason}
    // [
    //     ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""],
    //     ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""],
    //     ["", "", "", "", "", "", "", "X", "", "", "", "", "", "", ""],
    //     ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""],
    //     ["", "", "", "", "X", "", "", "", "", "", "", "", "", "", ""],
    //     ["", "", "", "", "", "", "", "", "", "O", "", "", "", "", ""],
    //     ["", "", "", "", "", "", "X", "", "", "", "", "", "", "", ""],
    //     ["", "", "", "", "", "", "", "O", "", "", "", "", "", "", ""],
    //     ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""],
    //     ["", "", "", "X", "", "", "", "", "", "", "", "", "", "", ""],
    //     ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""],
    //     ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""],
    //     ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""],
    //     ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""],
    //     ["O", "", "", "", "", "", "", "", "", "", "", "", "", "", ""]
    // ]

    public function new(Request $request)
    {
        $response = Http::post(url('/api/v1/games'), $request->all());

        if ($response->successful()) {
            $game = $response->json(); 
            return view('components.schemas.GameDetail', compact('game')); 
        } else {
            $error = $response->json(); 
            return back()->withErrors(['error' => $error['message'] ?? 'Došlo k chybě při vytvoření hry.']);
        }
    }

    public function newForm()
    {
        $title = 'Vytvořit novou hru';
        return view('components.schemas.NewUpdateGame', compact('title'));
    }

    //*  200 = 
    //*  {
    //*      "uuid": "67fda282-2bca-41ef-9caf-039cc5c8dd69",
    //*      "createdAt": "2025-01-03T17:03:19.527Z",
    //*      "updatedAt": "2025-01-03T17:03:19.527Z",
    //*      "name": "Moje první hra",
    //*      "difficulty": "hard",
    //*      "gameState": "midgame",
    //*      "board": "[15x[15x'']"
    //*  }
    //!  404 = Resource not found

    public function get($uuid)
    {
        $response = Http::get(url('/api/v1/game/$uuid')); 
        $game = $response->json();

        if ($response->successful()) 
            return view('components/schemas/Game', compact('game'));     
    }

    //*  200 = 
    //*  {
    //*      "uuid": "67fda282-2bca-41ef-9caf-039cc5c8dd69",
    //*      "createdAt": "2025-01-03T17:03:19.527Z",
    //*      "updatedAt": "2025-01-03T17:03:19.527Z",
    //*      "name": "Moje první hra",
    //*      "difficulty": "hard",
    //*      "gameState": "midgame",
    //*      "board": "[15x[15x'']"
    //*  }
    //!  400 = Bad request: ${reason}
    //!  404 = Resource not found
    //!  422 = Semantic error: ${reason}
    public function update(Request $request, $uuid)
    {
        $response = Http::put(url('/api/v1/games'), $request->all());
        $game = $response->json(); 

        if ($response->successful()) 
            return view('components.schemas.GameDetail', compact('game')); 
        
    }

    public function updateForm(Request $request, $uuid)
    {
        $title = 'Změnit hru';
        $data = Game::findOrFail($uuid);
        return view('components.schemas.UpdateGame', compact('title', 'data'));
    }

    //!  404 = Resource not found
    public function remove($uuid)
    {
        $response = Http::remove(url('/api/v1/game/$uuid')); 

        if ($response->successful()) 
            return redirect('/games');
    }


    private function updateGameState($board)
    {
        $xCount = 0;
        $oCount = 0;


        foreach ($board as $rowI => $row) {
            foreach ($row as $cellI => $cell) {
                $right = true;
                $down = true;
                $rightDown = true;

                if ($cell === 'X') {
                    $xCount++;
                } elseif ($cell === 'O') {
                    $oCount++;
                } else {
                    for ($i = 2; $i < 6; $i++) {
                        if (isset($board[$rowI][$cellI + $i]) &&($board[$rowI][$cellI + 1] == "X" || $board[$rowI][$cellI + 1] == "O")&& $board[$rowI][$cellI + 1] == $board[$rowI][$cellI + $i] && $right)
                            $right = true;
                        else
                            $right = false;


                        if (isset($board[$rowI + $i][$cellI])&&($board[$rowI + 1][$cellI] == "X" || $board[$rowI + 1][$cellI] == "O") && $board[$rowI + 1][$cellI] == $board[$rowI + $i][$cellI] && $down)
                            $down = true;
                        else
                            $down = false;

                        if (isset($board[$rowI + $i][$cellI + $i])&&($board[$rowI + 1][$cellI + 1] == "X" || $board[$rowI + 1][$cellI + 1] == "O") && $board[$rowI + 1][$cellI + 1] == $board[$rowI + $i][$cellI + $i] && $rightDown)
                            $rightDown = true;
                        else
                            $rightDown = false;
                    }

                    if ($right || $down || $rightDown)
                        if($right && isset($board[$rowI][$cellI + 5]) && $board[$rowI][$cellI + 5] == "")
                            return "endgame";
                        if($down && isset($board[$rowI + 5][$cellI]) && $board[$rowI + 5][$cellI] == "")
                            return "endgame";
                        if($rightDown && isset($board[$rowI + 5][$cellI + 5]) && $board[$rowI + 5][$cellI + 5] == "")
                            return "endgame";
                        
                }
            }
        }
        if ($xCount != $oCount && $xCount != $oCount + 1)
            return null;

        if ($oCount < 5)
            return "opening";
        else {
            return "midgame";
        }
    }
}
