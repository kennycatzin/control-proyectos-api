<?php

namespace App\Exports;

use App\Empresa;
use Maatwebsite\Excel\Concerns\FromCollection;
use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class EmpresasExport implements FromView
{
    public function view(): View
    {
        $data = DB::table('empresa')
        ->join('localidad', 'localidad.id_localidad', '=', 'empresa.id_localidad')
        ->join('municipio', 'municipio.id_municipio', '=', 'localidad.id_municipio')
        ->join('consultor', 'consultor.id_consultor', '=', 'empresa.id_consultor')
        ->select('empresa.*', 'localidad.localidad', 'municipio.municipio', 'consultor.empresa_consultora')
        ->where('empresa.activo', true)
        ->get();
        
       // Empresa::all();
        foreach($data as $dato){
            if($dato->experiencia_calidad_higienica == 1){
                $dato->experiencia_calidad_higienica = "Si, con certificaciones";
            }else if($dato->experiencia_calidad_higienica == 0){
                $dato->experiencia_calidad_higienica = "No";
            }elseif($dato->experiencia_calidad_higienica == 2){
                $dato->experiencia_calidad_higienica = "Si, pero no recientemente";
            }

            if($dato->tipo_registro == 1){
                $dato->tipo_registro = "Nuevo registro";
            }else{
                $dato->tipo_registro = "Renovación";
            }

            if($dato->alta_inventur == 1){
                $dato->alta_inventur = "Si";
            }else{
                $dato->alta_inventur = "No";
            }

            if($dato->rnt == 1){
                $dato->rnt = "Si";
            }elseif($dato->rnt == 0){
                $dato->rnt = "No";
            }elseif($dato->rnt == 2){
                $dato->rnt = "En trámite";
            }
        }

        return view('excel', [
            'empresas' => $data
        ]);
    }
}