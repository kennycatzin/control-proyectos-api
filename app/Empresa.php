<?php

namespace App;
use Illuminate\Database\Eloquent\Model;
class Empresa extends Model
{
    public $timestamps = false;
    protected $table = 'empresa';

    protected $fillable = [
       'id_empresa', 
       "nombre_comercial",
       "direccion",
       "razon_social",
       "municipio",
       "rfc",
       "telefono",
       "nombre_participante",
       "apellido_paterno",
       "apellido_materno",
       "email",
       "experiencia_calidad_higienica",
       "tipo_registro",
       "rnt",
       "folio_rnt",
       "alta_inventur",
       "giro_turistico"
    ];
    protected $hidden = [  
    ];
}

