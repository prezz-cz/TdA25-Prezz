<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Http\Requests\GameRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;


class GameController extends Controller
{

    //*  200 = Vsechny hry
    public function getAll()
    {
        $games = Game::all();
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
        // Převod textarea obsahu na pole
        $request['board'] = json_decode($request['board'], true);

        $validator400 = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'difficulty' => 'required',
                'board' => 'required',
            ],
            [
                'name.required' => 'The game name is required.',
                'difficulty.required' => 'The difficulty level is required.',
                'board.required' => 'The game board is required.',
            ]
        );

        $validator422 = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'difficulty' => 'in:beginner,easy,medium,hard,extreme',
            'board' => 'array|size:15',
            'board.*' => 'array|size:15',
            'board.*.*' => 'nullable|string|in:X,O,',
        ], [
            'name.string' => 'The game name must be a string.',
            'difficulty.in' => 'The difficulty level must be one of: beginner, easy, medium, hard, extreme.',
            'board.array' => 'The game board must be an array.',
            'board.size' => 'The game board must have exactly 15 rows.',
            'board.*.array' => 'Each row of the game board must be an array.',
            'board.*.size' => 'Each row of the game board must have exactly 15 columns.',
            'board.*.*.in' => 'Each cell of the board must contain X, O, or an empty string.',
        ]);

        if ($validator400->fails()) {
            return response()->json([
                'status' => 400,
                'error' => 'Bad request',
                'messages' => $validator400->errors(),
            ], 400);
        } else if ($validator422->fails()) {
            return response()->json([
                'status' => 422,
                'error' => 'Semantic error',
                'messages' => $validator422->errors(),
            ], 422);
        }

        $gameState = $this->updateGameState($request['board']);
        if ($gameState == null)
            return response()->json([
                'status' => 422,
                'error' => 'Semantic error',
                'messages' => 'The count of X must be same or 1 higher than O',
            ], 422);

        // Vytvoření nové hry
        $game = new Game([
            'uuid' => Str::uuid(),
            'createdAt' => now(),
            'name' => $request['name'],
            'difficulty' => $request['difficulty'],
            'gameState' => $gameState,
            'board' => $request['board'],
        ]);

        if ($game->save()) {
            return redirect('/games')->with('success', 'Game created successfully!');
        } else {
            dd('Game save failed');
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
        $game = Game::findOrFail($uuid);

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
        $request['board'] = json_decode($request['board'], true);

        $validator400 = Validator::make(
            $request->all(),
            [
                'name' => 'required',
                'difficulty' => 'required',
                'board' => 'required',
            ],
            [
                'name.required' => 'The game name is required.',
                'difficulty.required' => 'The difficulty level is required.',
                'board.required' => 'The game board is required.',
            ]
        );

        $validator422 = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'difficulty' => 'in:beginner,easy,medium,hard,extreme',
            'board' => 'array|size:15',
            'board.*' => 'array|size:15',
            'board.*.*' => 'nullable|string|in:X,O,',
        ], [
            'name.string' => 'The game name must be a string.',
            'difficulty.in' => 'The difficulty level must be one of: beginner, easy, medium, hard, extreme.',
            'board.array' => 'The game board must be an array.',
            'board.size' => 'The game board must have exactly 15 rows.',
            'board.*.array' => 'Each row of the game board must be an array.',
            'board.*.size' => 'Each row of the game board must have exactly 15 columns.',
            'board.*.*.in' => 'Each cell of the board must contain X, O, or an empty string.',
        ]);

        if ($validator400->fails()) {
            return response()->json([
                'status' => 400,
                'error' => 'Bad request',
                'messages' => $validator400->errors(),
            ], 400);
        } else if ($validator422->fails()) {
            return response()->json([
                'status' => 422,
                'error' => 'Semantic error',
                'messages' => $validator422->errors(),
            ], 422);
        }

        $gameState = $this->updateGameState($request['board']);
        if ($gameState == null)
            return response()->json([
                'status' => 422,
                'error' => 'Semantic error',
                'messages' => 'The count of X must be same or 1 higher than O',
            ], 422);
        
        $game = Game::findOrFail($uuid);
        $game->name = $request->input('name');
        $game->difficulty = $request->input('difficulty');
        $game->board = $request->input('board');
        $game->updatedAt = now();
        $game->gameState = $gameState;
        $game->update();
        return redirect("/games/$uuid");
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
        $game = Game::findOrFail($uuid);
        $game->delete();
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
