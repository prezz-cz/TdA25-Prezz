<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Game extends Model
{
    use HasFactory;
    //Obsahuje: 
    // UUID - string, format UUID, readOnly, notNull
    // createdAt - string, format date-time, readOnly, notNull
    // updatedAt - string, format date-time, readOnly
    // name - string, notNull
    // difficulty - string[beginner, easy, medium, hard, extreme], notNull
    // gameState - string[opening, midgame, endgame, unknown], notNull, readOnly
    // board [15x[15x'']]

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    //primaryKey
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'uuid';


    //dava uzivatel
    protected $fillable = [
        'uuid',
        'name',
        'difficulty',
        'gameState',
        'board',
        'createdAt',
        'updatedAt',
    ];


    //auto pretypovani
    protected $casts = [
        'board' => 'array',
        'createdAt' => 'datetime',
        'updatedAt' => 'datetime',
    ];

    // Automatická inicializace při vytvoření nové hry
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($game) {
            $game->uuid = (string) Str::uuid();
            $game->createdAt = now();
            $game->updatedAt = now();
        });
    }
    //auto cas
    public $timestamps = true;
}
