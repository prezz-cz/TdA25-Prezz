@extends('components.layouts.Base') 

@section('title', $title)

@section('content')

<form id="gameForm" action="/games" method="POST">
    @csrf
    <input type="text" name="name" placeholder="Game name" required>
    <select name="difficulty">
        <option value="beginner">Beginner</option>
        <option value="easy">Easy</option>
        <option value="medium">Medium</option>
        <option value="hard">Hard</option>
        <option value="extreme">Extreme</option>
    </select>
    <textarea name="board" required></textarea>
    <button type="submit">Create Game</button>
</form>
@endsection