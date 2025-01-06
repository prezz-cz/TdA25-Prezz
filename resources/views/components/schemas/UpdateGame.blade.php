@extends('components.layouts.Base') 

@section('title', $title)

@section('content')

<form id="gameForm" action="/games/{{$data->uuid}}" method="POST">
    @csrf
    @method('PUT') 
    <input type="text" name="name" placeholder="Game name" value="{{$data->name}}" required>
    <select name="difficulty">
        <option value="beginner" {{ old('difficulty', $data->difficulty) == 'beginner' ? 'selected' : '' }}>Beginner</option>
        <option value="easy" {{ old('difficulty', $data->difficulty) == 'easy' ? 'selected' : '' }}>Easy</option>
        <option value="medium" {{ old('difficulty', $data->difficulty) == 'medium' ? 'selected' : '' }}>Medium</option>
        <option value="hard" {{ old('difficulty', $data->difficulty) == 'hard' ? 'selected' : '' }}>Hard</option>
        <option value="extreme" {{ old('difficulty', $data->difficulty) == 'extreme' ? 'selected' : '' }}>Extreme</option>
    </select>
    <textarea name="board" required>{{ json_encode($data->board) }}</textarea>
    <button type="submit">Create Game</button>
</form>
@endsection