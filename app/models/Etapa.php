<?php

namespace App\models;
use Illuminate\Database\Eloquent\Model;
class Etapa extends Model
{
    public $timestamps = false;
    protected $table = 'cat_etapa';

    protected $fillable = [
       'id', 'nombre', 'descripcion','documento_etapa_id'
    ];
    protected $hidden = [  
    ];
}