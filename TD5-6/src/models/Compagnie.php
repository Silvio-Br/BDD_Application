<?php

namespace gamepedia\models;
use Illuminate\Database\Eloquent\Model;
class Compagnie extends Model
{
    protected $table = 'company';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function jeux() {
        return $this->belongsToMany(Game::class,'game_developers','comp_id','game_id');
    }
}