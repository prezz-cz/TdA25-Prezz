@extends('components/layouts/Base')

@section('title', 'Všechny hry')

@section('content')
<table>
    <thead>
        <tr>
            <th>Game Name</th>
            <th>Difficulty</th>
            <th>Game State</th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($games as $game)
        <tr>
            <td>{{ $game->name }}</td>
            <td>{{ ucfirst($game->difficulty) }}</td>
            <td>{{ ucfirst($game->gameState) }}</td>
            <td>
                <a href="/games/{{ $game->uuid }}" style="color: white; background-color: green; border: none; padding: 10px 20px; cursor: pointer;">
                    Otevřít hru
                </a>
            </td>
            <td>
                <button onclick="deleteGame('{{ $game->uuid }}')" style="color: white; background-color: red; border: none; padding: 10px 20px; cursor: pointer;">
                    Smazat hru
                </button>
            </td>
            <td>
                <a href="/games/update/{{ $game->uuid }}" style="color: black; background-color: cyan; border: none; padding: 10px 20px; cursor: pointer;">
                    Upravit hru
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection