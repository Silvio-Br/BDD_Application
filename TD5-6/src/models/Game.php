<?php

namespace gamepedia\models;
use Illuminate\Database\Eloquent\Model;
class Game extends Model
{
    protected $table = 'game';
    protected $primaryKey = 'id';
    public $timestamps = false;

    public function personnages() {
        return $this->belongsToMany(Character::class,'game2character','game_id','character_id');
    }

    public function developers() {
        return $this->belongsToMany(Compagnie::class,'game_developers','game_id','comp_id');
    }

    public function publies() {
        return $this->belongsToMany(Compagnie::class, 'game_publishers','game_id','comp_id');
    }

    public function ratings() {
        return $this->belongsToMany(RatingGame::class, 'game2rating', 'game_id','rating_id');
    }

    public function comments() {
        return $this->belongsToMany(Comments::class, 'game2comment', 'id_game','id_comment');
    }

}