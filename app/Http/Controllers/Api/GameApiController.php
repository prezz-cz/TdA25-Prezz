<?php

namespace App\Http\Controllers\Api;

use App\Models\Game;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GameApiController extends Controller
{
    public function getAll()
    {
        $games = Game::all();
        return response()->json($games, 200);
    }

    public function get($uuid)
    {
        $game = Game::where('uuid', $uuid)->first();

        if (!$game) {
            return response()->json([
                'code' => 404,
                'message' => 'Resource not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return response()->json($game, 200);
    }

    public function new(Request $request)
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

        // Vytvoření nové hry
        $game = new Game([
            'uuid' => Str::uuid(),
            'createdAt' => now(),
            'name' => $request['name'],
            'difficulty' => $request['difficulty'],
            'gameState' => $gameState,
            'board' => $request['board'],
        ]);

        if ($game->save())
            return response()->json($game, 201);
        else
            dd('Game save failed');
    }


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
        if ($game->update())
            return response()->json($game, 200);
        else
            dd('Game update failed');
    }

    public function remove($uuid)
    {
        $game = Game::where('uuid', $uuid)->first();

        if (!$game) {
            return response()->json([
                'code' => 404,
                'message' => 'Resource not found'
            ], Response::HTTP_NOT_FOUND);
        }
        $game->delete();

        return response()->json($game, 204);
    }
}
