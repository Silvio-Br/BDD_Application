<?php

namespace gamepedia\models;
use Illuminate\Database\Eloquent\Model;
class Photo extends Model
{
    protected $table = 'photo';
    protected $primaryKey = 'id_photo';
    public $timestamps = false;

    public function annonce() {
        return $this->belongsTo('models\Annonce', 'id_annonce');
    }
}