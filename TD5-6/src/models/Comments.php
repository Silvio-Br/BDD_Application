<?php

namespace gamepedia\models;
use Illuminate\Database\Eloquent\Model;
class Comments extends Model
{
    protected $table = 'comment';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $hidden = ['pivot'];

    public function auteur() {
        return $this->belongsTo(User::class, 'id_user');
    }
    
}