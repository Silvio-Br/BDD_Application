<?php

namespace gamepedia\models;
use Illuminate\Database\Eloquent\Model;
class Plateformes extends Model
{
    protected $table = 'platform';
    protected $primaryKey = 'id';
    public $timestamps = true;
}