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
            $tipo;
            $alta;
            if($request["tipo_registro"] == 1){
                $tipo=true;
            }else{
                $tipo=false;
            }   
            if($request["alta_inventur"] == 1){
                $alta=true;
            }else{
                $alta=false;
            }
            DB::insert('insert into empresa 
            (nombre_comercial, direccion, razon_social, rfc, 
            telefono, nombre_participante, apellido_paterno, apellido_materno, 
            email, experiencia_calidad_higienica, tipo_registro, rnt, folio_rnt,
            alta_inventur, giro_turistico, activo, fecha_creacion, fecha_modificacion,
            usuario_creacion, usuario_modificacion, id_consultor, id_localidad) 
            values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 
            [$request["nombre_comercial"], $request["direccion"], $request["razon_social"], 
            $request["rfc"], $request["telefono"], $request["nombre_participante"], 
            $request["apellido_paterno"],$request["apellido_materno"], $request["email"], 
            $request["experiencia_calidad_higienica"], $tipo, $request["rnt"], 
            $request["folio_rnt"], $alta, $request["giro_turistico"], true, 
            $this->getHoraFechaActual(), $this->getHoraFechaActual(), 
            $request["nombre_participante"], $request["nombre_participante"], 
            $request["id_consultor"], $request["id_localidad"]]);

            $id_consultor= DB::table('consultor')
            ->select('correo')
            ->where('id_consultor', $request["id_consultor"])
            ->first();

            $datosCorreo = [
                'email' => $request["email"],
                'nombre' => "nombre",
                'subject' => 'Empresa registrada',
                'empresa_correo' => $id_consultor->correo,
                'ayuda1' => env('CORREO_AYUDA1',""),
                'ayuda2' => env('CORREO_AYUDA2',""),
            ];
            $datosVista = [
                'empresa' => $request["nombre_comercial"],
                'nombre' => $request["nombre_participante"].' '.$request["apellido_paterno"],
            ];
            $this->enviarCorreo($datosVista,$datosCorreo);
            return $this->crearRespuesta("Registro insertado", 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("Ha ocurrido un problema ". $th->getMessage(). ' '.$th->getLine(), 500);
        }
    }
    public function getListadoEmpresas($index){
        try {
            $cuenta =  DB::table('empresa')
            ->select("id_empresa", "nombre_comercial", DB::raw("CONCAT(nombre_participante,' ', apellido_paterno) AS nombre"), "email", "giro_turistico")
            ->where('activo', true)
            ->count();
            $data = DB::table('empresa')
            ->select("id_empresa", "nombre_comercial", DB::raw("CONCAT(nombre_participante,' ', apellido_paterno) AS nombre"), "email", "giro_turistico")
            ->where('activo', true)
            ->skip($index)
            ->take(5)
            ->get();
            return response()->json(['data'=>$data, 'total'=>$cuenta, 'ok'=>true], 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("Ha ocurrido un problema ". $th->getMessage(). ' '.$th->getLine(), 500);
        }
    }
    public function getDetalleEmpresa($id_empresa){
        try {
            $data = $data = DB::table('empresa')
            ->join('localidad', 'localidad.id_localidad', '=', 'empresa.id_localidad')
            ->join('municipio', 'municipio.id_municipio', '=', 'localidad.id_municipio')
            ->join('consultor', 'consultor.id_consultor', '=', 'empresa.id_consultor')
            ->select('empresa.*', 'localidad.localidad', 'municipio.municipio', 'consultor.empresa_consultora')
            ->where('empresa.activo', true)
            ->where("empresa.id_empresa", $id_empresa)
            ->get();
            foreach($data as $dato){
                $dato->fecha_creacion = date("d-m-Y", strtotime($dato->fecha_creacion));
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
            return $this->crearRespuesta($data[0], 200);
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
            $busqueda = $request["buscador"];
            $data = DB::table('empresa')
            ->select("id_empresa", 'activo', "nombre_comercial", DB::raw("CONCAT(nombre_participante,' ', apellido_paterno) AS nombre"), "email", "giro_turistico")
            ->orWhere('nombre_comercial', 'LIKE', '%'.$busqueda.'%')
            ->orWhere('razon_social', 'LIKE',  '%'.$busqueda.'%')
            ->take(8)
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
    public function getMunicipios(){
        try {
            
            $data = DB::table('municipio')
            ->select('id_municipio', 'municipio')
            ->get();

            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function getLocalidades($id_municipio){
        try {
            $data = DB::table('localidad')
            ->select('id_localidad', 'localidad')
            ->where('id_municipio', $id_municipio)
            ->get();
            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    
    public function getGirosTuristicos(){
        try {
            $data = array(
                "Hospedaje (Hoteles, haciendas).",

                "Agencias de viaje y módulos de información.",

                "Guías de Turistas",

                "Reuniones (MICE, recintos, salones, DMC's).",

                "Taxis o plataformas digitales de transporte.",

                "Transporte (no taxis o plataformas digitales).",

                "Alimentos y bebidas (Restaurantes, bares, banquetes)",

                "Módulo de información",

                "Bodas, romance y eventos sociales"
            );
            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    public function getConsultores(){
        try {
            $data = DB::table('consultor')
            ->select('id_consultor', 'empresa_consultora', 'correo')
            ->get();
            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    
}