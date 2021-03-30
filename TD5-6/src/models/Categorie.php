<?php

namespace gamepedia\models;
use Illuminate\Database\Eloquent\Model;
class Categorie extends Model
{
    protected $table = 'categorie';
    protected $primaryKey = 'id_categorie';
    public $timestamps = false;

    public function annonces() {
        return $this->belongsToMany('models\Annonce', 'appartenanceCategorieAnnonce', 'id_categorie','id_annonce');
    }
}