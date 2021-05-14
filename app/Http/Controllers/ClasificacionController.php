<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Exports\EmpresasExport;
use Maatwebsite\Excel\Facades\Excel;
class ClasificacionController extends Controller
{
   
    public function getClasificaciones(){
        $data = DB::table('cat_clasificacion')
        ->select('*')
        ->get();

        return $this->crearRespuesta($data, 200);
    }
    public function getEstatus(){
        $data = DB::table('cat_status')
        ->select('*')
        ->get();

        return $this->crearRespuesta($data, 200);
    }

    
}