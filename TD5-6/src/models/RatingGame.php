<?php

namespace gamepedia\models;
use Illuminate\Database\Eloquent\Model;
class RatingGame extends Model
{
    protected $table = 'game_rating';
    protected $primaryKey = 'id';
    public $timestamps = false;

}