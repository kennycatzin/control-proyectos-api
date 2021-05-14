<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Exports\EmpresasExport;
use Maatwebsite\Excel\Facades\Excel;
class InicioController extends Controller
{
   
    public function getTablero($tipo){
        $contador = 0;
        $contador2= 0;

        $this->actualizaCompromisosVencidos();
        $this->actualizaPendientes();
        $estatus = DB::table('cat_status')
        ->select('*')
        ->get();
        $estComp = $estatus;

       

        foreach($estatus as $estado){
            if($contador == 0){
                $estatus=json_decode(json_encode($estatus), true);
            }
            $total = DB::table('pendiente')
            ->where('activo', true)
            ->where('id_status', $estado->id_status)
            ->where('tipo_pendiente', $tipo)
            ->count();
            $estatus[$contador]+=["cantidad"=>$total]; 
            $contador++;
        }
        foreach($estComp as $eCom){
            if($contador2 == 0){
                $estComp=json_decode(json_encode($estComp), true);
            }
            $total2 = DB::table('compromiso')
            ->join('pendiente', 'pendiente.id_pendiente', 'compromiso.id_pendiente')
            ->where('compromiso.activo', true)
            ->where('pendiente.tipo_pendiente', $tipo)
            ->where('compromiso.id_status', $eCom->id_status)
            ->count();
            $estComp[$contador2]+=["cantidad"=>$total2]; 
            $contador2++;
        }
        $vencidos = DB::table('compromiso')
        ->join('pendiente', 'pendiente.id_pendiente', 'compromiso.id_pendiente')
        ->select('compromiso.id_compromiso', 'compromiso.fecha', 'compromiso.compromiso')
        ->where('compromiso.activo', true)
        ->where('pendiente.tipo_pendiente', $tipo)
        ->where('compromiso.id_status', 3)
        ->take(20)
        ->orderBy('id_compromiso', 'DESC')
        ->get();

        $movimientos = DB::table('movimientos')
        ->select('id_movimiento', 'movimiento', 'descripcion')
        ->where('activo', true)
        ->take(20)
        ->orderBy('id_movimiento', 'DESC')
        ->get();

    
        return response()->json(['pendientes'=>$estatus, 'compromisos' => $estComp, 'vencidos'=> $vencidos, 'movimientos'=> $movimientos, 'ok'=>true], 200);
    }

    
}