<?php

namespace gamepedia\models;
use Illuminate\Database\Eloquent\Model;
class Annonce extends Model
{
    protected $table = 'annonce';
    protected $primaryKey = 'id_annonce';
    public $timestamp = false;

    public function photos() {
        return $this->hasMany('models\Photo', 'id_annonce');
    }

    public function categories() {
        return $this->belongsToMany('models\Categorie','appartenanceCategorieAnnnonce', 'id_annonce', 'id_categorie');
    }
}