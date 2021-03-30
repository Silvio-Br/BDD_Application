<?php

namespace gamepedia\models;
use Illuminate\Database\Eloquent\Model;
class RatingBoard extends Model
{
    protected $table = 'rating_board';
    protected $primaryKey = 'id';
    public $timestamps = false;

}