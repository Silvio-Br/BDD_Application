<?php

namespace gamepedia\models;
use Illuminate\Database\Eloquent\Model;
class Genre extends Model
{
    protected $table = 'genre';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function genres() {
        $this->belongsToMany(Game::class, 'game2genre','genre_id', 'game_id');
    }

}