<?php

namespace gamepedia\models;
use Illuminate\Database\Eloquent\Model;
class Character extends Model
{
    protected $table = 'character';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function jeux() {
        $this->belongsToMany(Game::class, 'game2character','id','id');
    }

}