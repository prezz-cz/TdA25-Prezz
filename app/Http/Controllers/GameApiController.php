<?php

namespace App\Http\Controllers;

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
        // Validační pravidla a jejich aplikace
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
            'board' => 'array|size:15', // Validace počtu řádků
            'board.*' => 'array|size:15', // Validace počtu sloupců v každém řádku
            'board.*.*' => 'nullable|string|in:X,O, null, "",', // Validace obsahu každé buňky
        ], [
            'name.string' => 'The game name must be a string.',
            'difficulty.in' => 'The difficulty level must be one of: beginner, easy, medium, hard, extreme.',
            'board.array' => 'The game board must be an array.',
            'board.size' => 'The game board must have exactly 15 rows.',
            'board.*.array' => 'Each row of the game board must be an array.',
            'board.*.size' => 'Each row of the game board must have exactly 15 columns.',
            'board.*.*.in' => 'Each cell of the board must contain X, O, or an empty string.',
        ]);
        

        // Validace: pokud některá pravidla selžou
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
    
        // Získání validovaných dat
        $data = $validator400->validated();
        
        // Ověření gameState
        $gameState = $this->updateGameState($data['board']);
        if ($gameState == null) {
            return response()->json([
                'status' => 422,
                'error' => 'Semantic error',
                'messages' => 'The count of X must be same or 1 higher than O',
            ], 422);
        }
        

        foreach ($data['board'] as &$row) { 
            foreach ($row as &$column) { 
                if ($column === null) {
                    $column = ''; 
                }
            }
        }
        unset($row);    
        unset($column); 

        $game = new Game([
            'uuid' => Str::uuid(),
            'createdAt' => now(),
            'name' => $data['name'],
            'difficulty' => $data['difficulty'],
            'gameState' => $gameState, // Příklad
            'board' => $data['board'],
        ]);
    
        if ($game->save()) {
            // return response()->json($game, 201);
            return response($game, 201);

        } else {
            return response()->json(['error' => 'Game save failed'], 500);
        }
    }
    


    public function update($uuid, Request $request)
    {
        // Dekódování JSON formátu 'board' na PHP pole
    
        // Validace 400: Základní validace, jestli jsou požadované hodnoty přítomné
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
    
        // Validace 422: Validace hodnot a formátu dat
        $validator422 = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'difficulty' => 'in:beginner,easy,medium,hard,extreme',
            'board' => 'array|size:15', // Board musí mít 15 řádků
            'board.*' => 'array|size:15', // Každý řádek musí mít 15 sloupců
            'board.*.*' => 'nullable|string|in:X,O,', // Hodnoty: null, X, O, nebo prázdný řetězec
        ], [
            'name.string' => 'The game name must be a string.',
            'difficulty.in' => 'The difficulty level must be one of: beginner, easy, medium, hard, extreme.',
            'board.array' => 'The game board must be an array.',
            'board.size' => 'The game board must have exactly 15 rows.',
            'board.*.array' => 'Each row of the game board must be an array.',
            'board.*.size' => 'Each row of the game board must have exactly 15 columns.',
            'board.*.*.in' => 'Each cell of the board must contain X, O, or an empty string.',
        ]);
    
        // Chyba 400: Pokud základní validace selže
        if ($validator400->fails()) {
            return response()->json([
                'status' => 400,
                'error' => 'Bad request',
                'messages' => $validator400->errors(),
            ], 400);
        }
    
        // Chyba 422: Pokud validace hodnot a formátu selže
        if ($validator422->fails()) {
            return response()->json([
                'status' => 422,
                'error' => 'Semantic error',
                'messages' => $validator422->errors(),
            ], 422);
        }
    
        // Validace stavu hry: Kontrola počtu X a O
        $gameState = $this->updateGameState($request['board']);
        if ($gameState == null) {
            return response()->json([
                'status' => 422,
                'error' => 'Semantic error',
                'messages' => 'The count of X must be the same or 1 higher than O',
            ], 422);
        }
        $board = $request->input('board');

        foreach ($board as &$row) { 
            foreach ($row as &$column) { 
                if ($column === null) {
                    $column = ''; 
                }
            }
        }
        // Najít hru podle UUID
        $game = Game::findOrFail($uuid);
        $game->name = $request->input('name');
        $game->difficulty = $request->input('difficulty');
        $game->board = $board;
        $game->updatedAt = now();
        $game->gameState = $gameState;
    
        // Uložit změny hry
        if ($game->update()) {
            return response()->json($game, 200);
        } else {
            // Pokud se aktualizace nepodaří
            return response()->json([
                'status' => 500,
                'error' => 'Internal Server Error',
                'message' => 'Failed to update the game.',
            ], 500);
        }
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

    private function updateGameState($board)
    {
        $xCount = 0;
        $oCount = 0;
    
        foreach ($board as $rowI => $row) {
            foreach ($row as $cellI => $cell) {
                if ($cell === 'X') {
                    $xCount++;
                } elseif ($cell === 'O') {
                    $oCount++;
                } else {
                    // Zkontroluj všechny směry
                    if (
                        $this->checkDirection($board, $rowI, $cellI, 0, 1) || // Horizontálně
                        $this->checkDirection($board, $rowI, $cellI, 1, 0) || // Vertikálně
                        $this->checkDirection($board, $rowI, $cellI, 1, 1)    // Diagonálně
                    ) {
                        return "endgame";
                    }
                }
            }
        }
    
        // Validace počtu tahů
        if ($xCount != $oCount && $xCount != $oCount + 1) {
            return null; // Neplatná hrací deska
        }
    
        // Stav hry podle počtu tahů
        if ($oCount <= 4) {
            return "opening";
        } else {
            return "midgame";
        }
    }
    
    // Funkce pro kontrolu daného směru
    private function checkDirection($board, $rowI, $cellI, $rowDir, $colDir)
    {
        $player = null; // Hráč, kterého sledujeme
        $count = 0; // Počet po sobě jdoucích symbolů
    
        for ($i = 1; $i <= 4; $i++) {
            $nextRow = $rowI + $i * $rowDir;
            $nextCol = $cellI + $i * $colDir;
    
            if (!isset($board[$nextRow][$nextCol]) || $board[$nextRow][$nextCol] === '') {
                break; // Konec sekvence
            }
    
            if ($player === null) {
                $player = $board[$nextRow][$nextCol]; // Nastavíme hráče
            } elseif ($board[$nextRow][$nextCol] !== $player) {
                return false; // Není souvislá sekvence
            }
    
            $count++;
        }
    
        // Zkontroluj, zda je sekvence 4 dlouhá a z obou stran neblokovaná
        if ($count === 4) {
            $beforeRow = $rowI - $rowDir;
            $beforeCol = $cellI - $colDir;
            $afterRow = $rowI + 5 * $rowDir;
            $afterCol = $cellI + 5 * $colDir;
    
            $before = !isset($board[$beforeRow][$beforeCol]) || $board[$beforeRow][$beforeCol] === '';
            $after = !isset($board[$afterRow][$afterCol]) || $board[$afterRow][$afterCol] === '';
    
            return $before || $after; // Musí být alespoň z jedné strany otevřené
        }
    
        return false;
    }
    
}
