<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Exports\EmpresasExport;
use Maatwebsite\Excel\Facades\Excel;
class EmpresaController extends Controller
{
   
    public function storeEmpresa(Request $request){
        $this->validate($request, [
            'nombre_participante' => 'required',
            'apellido_paterno' => 'required',
            'nombre_comercial' => 'required'
        ]);
        try {
            DB::insert('insert into empresa 
            (nombre_comercial, direccion, razon_social, municipio, rfc, 
            telefono, nombre_participante, apellido_paterno, apellido_materno, 
            email, experiencia_calidad_higienica, tipo_registro, rnt, folio_rnt,
            alta_inventur, giro_turistico, activo, fecha_creacion, fecha_modificacion,
            usuario_creacion, usuario_modificacion) 
            values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 
            [$request["nombre_comercial"], $request["direccion"], $request["razon_social"], 
            $request["municipio"], $request["rfc"], $request["telefono"], 
            $request["nombre_participante"], $request["apellido_paterno"],$request["apellido_materno"],
            $request["email"], $request["experiencia_calidad_higienica"], $request["tipo_registro"], 
            $request["rnt"], $request["folio_rnt"], $request["alta_inventur"], $request["giro_turistico"], 
            true, $this->getHoraFechaActual(), $this->getHoraFechaActual(), $request["nombre_participante"], 
            $request["nombre_participante"]]);
            return $this->crearRespuesta("Registro insertado", 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("Ha ocurrido un problema ". $th->getMessage(). ' '.$th->getLine(), 500);
        }
    }
    public function getListadoEmpresas($index){
        try {
            $data = DB::table('empresa')
            ->select("id_empresa", "nombre_comercial", DB::raw("CONCAT(nombre_participante,' ', apellido_paterno) AS nombre"), "email", "giro_turistico")
            ->where('activo', true)
            ->skip($index)
            ->take(5)
            ->get();
            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("Ha ocurrido un problema ". $th->getMessage(). ' '.$th->getLine(), 500);
        }
    }
    public function getDetalleEmpresa($id_empresa){
        try {
            $data = DB::table('empresa')
            ->select("*")
            ->where("activo", true)
            ->where("id_empresa", $id_empresa)
            ->first();
            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("Ha ocurrido un problema ". $th->getMessage(). ' '.$th->getLine(), 500);
        }
    }
    public function bajaEmpresa(Request $request){
        try {    
            $id_empresa = $request["id_empresa"];
            $usuario = $request["usuario"];
            DB::update('update empresa 
            set activo = ?, fecha_modificacion = ?, usuario_modificacion = ?
            where id_empresa = ?', 
            [false, $this->getHoraFechaActual(), $usuario, $id_empresa]);
            return $this->crearRespuesta("La empresa ha sido dada de baja", 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("Ha ocurrido un problema ". $th->getMessage(). ' '.$th->getLine(), 500);
        }
    }
    public function busquedaPorNombre(Request $request){
        try {    
            $data = DB::table('empresa')
            ->select("id_empresa", 'activo', "nombre_comercial", DB::raw("CONCAT(nombre_participante,' ', apellido_paterno) AS nombre"), "email", "giro_turistico")
            ->where('activo', "=", true)
            ->orWhere('nombre_comercial', 'like', '%'.$request["busqueda"].'%')
            ->orWhere('razon_social', 'like', '%'.$request["busqueda"].'%')
            ->orWhere('nombre_participante', 'like', '%'.$request["busqueda"].'%')
            ->orWhere('apellido_paterno', 'like', '%'.$request["busqueda"].'%')
            ->take(5)
            ->get();
            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("Ha ocurrido un problema ". $th->getMessage(). ' '.$th->getLine(), 500);
        }
    }

    public function getEmpresaItem($id_empresa){
        try {
            $data = DB::table('empresa')
            ->select("id_empresa", "nombre_comercial", DB::raw("CONCAT(nombre_participante,' ', apellido_paterno) AS nombre"), "email", "giro_turistico")
            ->where('activo', true)
            ->where('id_empresa', $id_empresa)
            ->get();
            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("Ha ocurrido un problema ". $th->getMessage(). ' '.$th->getLine(), 500);
        }
    }
    public function getEmpresasExcel(){
        try {
            return Excel::download(new EmpresasExport, 'empresas.xlsx');
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("Ha ocurrido un problema ". $th->getMessage(). ' '.$th->getLine(), 500);
        }
    }
}