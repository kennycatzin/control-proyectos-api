<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Exports\EmpresasExport;
use Maatwebsite\Excel\Facades\Excel;
class BitacoraController extends Controller
{
   
    public function getBitacoraCompromisos($index){
        $contador = 0;
        $data = DB::table('movimientos')
        ->select('*')
        ->where('activo', true)
        ->where('tipo', 2)
        ->orderBy('id_movimiento', 'DESC')
        ->skip($index)
        ->take(6)
        ->get();
        foreach($data as $miData){
            if($contador == 0){
                $data=json_decode(json_encode($data), true);
            }
            $observacion = DB::table('observaciones_compromisos')
            ->select('id_observaciones_compromisos','descripcion')
            ->where('activo', true)
            ->where('id_movimiento', $miData->id_movimiento)
            ->first();
            if (empty($observacion)){
                $data[$contador]+=["observacion"=>null];
            }else{
                $data[$contador]["activo"] = $observacion->descripcion;
                $data[$contador]+=["observacion"=>$observacion];
            }
            $contador++;
        }
        return $this->crearRespuesta($data, 200);
    }
    public function getBitacoraPendientes($index){
        $contador = 0;
        $data = DB::table('movimientos')
        ->select('*')
        ->where('activo', true)
        ->where('tipo', 1)
        ->skip($index)
        ->take(6)
        ->get();
        foreach($data as $miData){
            if($contador == 0){
                $data=json_decode(json_encode($data), true);
            }
            $observacion = DB::table('observaciones_pendientes')
            ->select('id_observaciones_pendientes', 'descripcion')
            ->where('activo', true)
            ->where('id_movimiento', $miData->id_movimiento)
            ->first();
            if (empty($observacion)){
                $data[$contador]+=["observacion"=>null];
            }else{
                $data[$contador]["activo"] = $observacion->descripcion;
                $data[$contador]+=["observacion"=>$observacion];
            }
            $contador++;
        }
        return $this->crearRespuesta($data, 200);
    }
    public function getPendientesDia(Request $request){
        $contador = 0;
        $busqueda = $request["nombre"];
        $fecha = $request["fecha"];
        $data;
        if(strlen($busqueda) > 0 && strlen($fecha) == 0){
            $data = DB::table('movimientos')
            ->select('*')
            ->where('activo', true)
            ->where('tipo', 1)
            ->Where('usuario_modificacion', 'LIKE', '%'.$busqueda.'%')
            ->get();
        }else if(strlen($busqueda) == 0 && strlen($fecha) > 0){
            $data = DB::table('movimientos')
            ->select('*')
            ->where('activo', true)
            ->where('tipo', 1)
            ->whereBetween('fecha_modificacion', [$fecha." 00:00:00",$fecha." 23:59:59"])
            ->get();
        } else if(strlen($busqueda) > 0 && strlen($fecha) > 0){
            $data = DB::table('movimientos')
            ->select('*')
            ->where('activo', true)
            ->where('tipo', 1)
            ->Where('usuario_modificacion', 'LIKE', '%'.$busqueda.'%')
            ->whereBetween('fecha_modificacion', [$fecha." 00:00:00",$fecha." 23:59:59"])
            ->get();
        }
        foreach($data as $miData){
            if($contador == 0){
                $data=json_decode(json_encode($data), true);
            }
            $observacion = DB::table('observaciones_pendientes')
            ->select('id_observaciones_pendientes', 'descripcion')
            ->where('activo', true)
            ->where('id_movimiento', $miData->id_movimiento)
            ->first();
            if (empty($observacion)){
                $data[$contador]+=["observacion"=>null];
            }else{
                $data[$contador]+=["observacion"=>$observacion];
                $data[$contador]["activo"] = $observacion->descripcion;

            }
            $contador++;
        }
        return $this->crearRespuesta($data, 200);
    }
    public function getPendientesPeriodo(Request $request){
        $contador = 0;
        $busqueda = $request["nombre"];
        $fechaInicial = $request["fechaInicial"];
        $fechaFinal = $request["fechaFinal"];

        $data;
        if(strlen($busqueda) > 0 && strlen($fechaInicial) == 0 && strlen($fechaFinal) == 0){
            $data = DB::table('movimientos')
            ->select('*')
            ->where('activo', true)
            ->where('tipo', 1)
            ->Where('usuario_modificacion', 'LIKE', '%'.$busqueda.'%')
            ->get();
        }else if(strlen($busqueda) == 0 && strlen($fechaInicial) > 0 && strlen($fechaFinal) > 0){
            $data = DB::table('movimientos')
            ->select('*')
            ->where('activo', true)
            ->where('tipo', 1)
            ->whereBetween('fecha_modificacion', [$fechaInicial." 00:00:00",$fechaFinal." 23:59:59"])
            ->get();
        } else if(strlen($busqueda) > 0 && strlen($fechaInicial) > 0 && strlen($fechaFinal) > 0){
            $data = DB::table('movimientos')
            ->select('*')
            ->where('activo', true)
            ->where('tipo', 1)
            ->Where('usuario_modificacion', 'LIKE', '%'.$busqueda.'%')
            ->whereBetween('fecha_modificacion', [$fechaInicial." 00:00:00",$fechaFinal." 23:59:59"])
            ->get();
        }
        foreach($data as $miData){
            if($contador == 0){
                $data=json_decode(json_encode($data), true);
            }
            $observacion = DB::table('observaciones_pendientes')
            ->select('id_observaciones_pendientes', 'descripcion')
            ->where('activo', true)
            ->where('id_movimiento', $miData->id_movimiento)
            ->first();
            if (empty($observacion)){
                $data[$contador]+=["observacion"=>null];
            }else{
                $data[$contador]["activo"] = $observacion->descripcion;
                $data[$contador]+=["observacion"=>$observacion];
            }
            $contador++;
        }
        return $this->crearRespuesta($data, 200);
    }
    public function getCompromisosDia(Request $request){
        $contador = 0;
        $busqueda = $request["nombre"];
        $fecha = $request["fecha"];
        $data;
        if(strlen($busqueda) > 0 && strlen($fecha) == 0){
            $data = DB::table('movimientos')
            ->select('*')
            ->where('activo', true)
            ->where('tipo', 2)
            ->Where('usuario_modificacion', 'LIKE', '%'.$busqueda.'%')
            ->get();
        }else if(strlen($busqueda) == 0 && strlen($fecha) > 0){
            $data = DB::table('movimientos')
            ->select('*')
            ->where('activo', true)
            ->where('tipo', 2)
            ->whereBetween('fecha_modificacion', [$fecha." 00:00:00",$fecha." 23:59:59"])
            ->get();
        } else if(strlen($busqueda) > 0 && strlen($fecha) > 0){
            $data = DB::table('movimientos')
            ->select('*')
            ->where('activo', true)
            ->where('tipo', 2)
            ->Where('usuario_modificacion', 'LIKE', '%'.$busqueda.'%')
            ->whereBetween('fecha_modificacion', [$fecha." 00:00:00",$fecha." 23:59:59"])
            ->get();
        }
        foreach($data as $miData){
            if($contador == 0){
                $data=json_decode(json_encode($data), true);
            }
            $observacion = DB::table('observaciones_compromisos')
            ->select('id_observaciones_compromisos','descripcion')
            ->where('activo', true)
            ->where('id_movimiento', $miData->id_movimiento)
            ->first();
            if (empty($observacion)){
                $data[$contador]+=["observacion"=>null];
            }else{
                $data[$contador]["activo"] = $observacion->descripcion;
                $data[$contador]+=["observacion"=>$observacion];
            }
            $contador++;
        }
        return $this->crearRespuesta($data, 200);
    }

    public function getCompromisosPeriodo(Request $request){
        $contador = 0;
        $busqueda = $request["nombre"];
        $fechaInicial = $request["fechaInicial"];
        $fechaFinal = $request["fechaFinal"];

        $data;
        if(strlen($busqueda) > 0 && strlen($fechaInicial) == 0 && strlen($fechaFinal) == 0){
            $data = DB::table('movimientos')
            ->select('*')
            ->where('activo', true)
            ->where('tipo', 2)
            ->Where('usuario_modificacion', 'LIKE', '%'.$busqueda.'%')
            ->get();
        }else if(strlen($busqueda) == 0 && strlen($fechaInicial) > 0 && strlen($fechaFinal) > 0){
            $data = DB::table('movimientos')
            ->select('*')
            ->where('activo', true)
            ->where('tipo', 2)
            ->whereBetween('fecha_modificacion', [$fechaInicial." 00:00:00",$fechaFinal." 23:59:59"])
            ->get();
        } else if(strlen($busqueda) > 0 && strlen($fechaInicial) > 0 && strlen($fechaFinal) > 0){
            $data = DB::table('movimientos')
            ->select('*')
            ->where('activo', true)
            ->where('tipo', 2)
            ->Where('usuario_modificacion', 'LIKE', '%'.$busqueda.'%')
            ->whereBetween('fecha_modificacion', [$fechaInicial." 00:00:00",$fechaFinal." 23:59:59"])
            ->get();
        }
        foreach($data as $miData){
            if($contador == 0){
                $data=json_decode(json_encode($data), true);
            }
            $observacion = DB::table('observaciones_compromisos')
            ->select('id_observaciones_compromisos','descripcion')
            ->where('activo', true)
            ->where('id_movimiento', $miData->id_movimiento)
            ->first();
            if (empty($observacion)){
                $data[$contador]+=["observacion"=>null];
            }else{
                $data[$contador]["activo"] = $observacion->descripcion;
                $data[$contador]+=["observacion"=>$observacion];
            }
            $contador++;
        }
        return $this->crearRespuesta($data, 200);
    }

    
}