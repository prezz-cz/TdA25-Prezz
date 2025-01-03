<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'name', 'difficulty', 'game_state', 'board'
    ];

    protected $casts = [
        'board' => 'array',
    ];

    public $incrementing = false; 
    protected $keyType = 'string'; 
}
