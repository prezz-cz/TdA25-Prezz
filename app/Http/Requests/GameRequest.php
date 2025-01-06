<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class GameRequest extends FormRequest
{

    public function authorize()
    {
        return true; 
    }


    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'difficulty' => 'required|in:beginner,easy,medium,hard,extreme',
            'board' => 'required|array|size:15',
            'board.*' => 'array|size:15',
            'board.*.*' => 'nullable|string|in:X,O,',
        ];
    }


    public function messages()
    {
        return [
            'name.required' => 'The game name is required.',
            'name.string' => 'The game name must be a string.',
            'difficulty.required' => 'The difficulty level is required.',
            'difficulty.in' => 'The difficulty level must be one of: beginner, easy, medium, hard, extreme.',
            'board.required' => 'The game board is required.',
            'board.array' => 'The game board must be an array.',
            'board.size' => 'The game board must have exactly 15 rows.',
            'board.*.array' => 'Each row of the game board must be an array.',
            'board.*.size' => 'Each row of the game board must have exactly 15 columns.',
            'board.*.*.in' => 'Each cell of the board must contain X, O, or an empty string.',
        ];
    }


    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();

        if ($errors->has('required')) 
            $statusCode = 400; 
        else
            $statusCode = 422;

        throw new HttpResponseException(
            response()->json([
                'status' => $statusCode, 
                'message' => 'Bad request: Validation failed.',
                'errors' => $validator->errors(), 
            ], $statusCode)
        );
    }
}
