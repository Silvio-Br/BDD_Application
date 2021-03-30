<?php

namespace gamepedia\models;
use Illuminate\Database\Eloquent\Model;
class User extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    public function comments() {
        return $this->belongsToMany(Comments::class, 'user2comment', 'id_user', 'id_comment');
    }

}