<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Exports\EmpresasExport;
use Illuminate\Http\File as File;
use Maatwebsite\Excel\Facades\Excel;
class PendienteController extends Controller
{
   
    public function storePendiente(Request $request){
        DB::insert('insert into pendiente
        (id_status, id_clasificacion, id_solicitante, titulo, descripcion,
        tipo_pendiente, prioridad, solicitante, fecha, archivo, num_compromisos, activo, 
        usuario_creacion, usuario_modificacion, fecha_creacion, fecha_modificacion)
        values (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)', 
        [1, $request["id_clasificacion"], $request["id_solicitante"],
        $request["titulo"], $request["descripcion"], $request["tipo_pendiente"],
        $request["prioridad"], $request["solicitante"], $request["fecha"], "", 0,
        true, $request["usuario"], $request["usuario"], $this->getHoraFechaActual(), 
        $this->getHoraFechaActual()]);
        $descripcion = 'El usuario '.$request['usuario'].' ha creado el pendiente '.$request["titulo"];
        $id_pendiente = DB::getPdo()->lastInsertId();
        // $this->fileUpload($request, $id_pendiente);
        $id_movimiento =$this->guardarMovimiento('insertar', 1, $descripcion);
        return response()->json(['data'=>"El elemento se ha registrado correctamente", 'id_pendiente'=> $id_pendiente,'id_movimiento'=>$id_movimiento, 'ok'=>true], 200);
    }
    public function fileUpload(Request $request, $id) {
        $response = null;
            if ($request->hasFile('archivo')) {
                $original_filename = $request->file('archivo')->getClientOriginalName();
                $original_filename_arr = explode('.', $original_filename);
                $file_ext = end($original_filename_arr);
                $destination_path = './upload/pendientes/';
                $image = 'P-' . $id . '.' . $file_ext;
                if ($request->file('archivo')->move($destination_path, $image)) {
                    // $user->image = './upload/pendientes/'.$image;
                    // $pasiente->imagen = $image;
                    $data = DB::table('pendiente')
                        ->select('archivo')
                        ->where('activo', true)
                        ->where('id_pendiente', $id)
                        ->first();
                    if(file_exists("upload/pendientes/".$data->archivo)) {
                        @unlink("upload/pendientes/".$data->archivo);
                    }
                    DB::update('update pendiente set archivo = ? where id_pendiente = ?', 
                    [$image, $id]);
                   // $pasiente->save();
                    // return $this->crearRespuesta('El archivo ha sido subida con éxito', 201);
                } else {
                   // return $this->crearRespuestaError('Ha ocurrido un error con la imagen', 400);
                }
            } else {
               // return $this->crearRespuestaError('No existe el archivo', 400);
            }
    }
    public function editarPendiente(Request $request){
        DB::update('update pendiente set 
        id_status = ?, id_clasificacion = ?, id_solicitante = ?, titulo = ?, descripcion = ?,
        tipo_pendiente = ?, prioridad = ?, fecha = ?,
        usuario_modificacion = ?, fecha_modificacion = ?, solicitante= ?
        where id_pendiente = ?', 
        [$request["id_status"], $request["id_clasificacion"], $request["id_solicitante"], $request["titulo"],
        $request["descripcion"], $request["tipo_pendiente"], $request["prioridad"],
        $request["fecha"], $request["usuario"],  $this->getHoraFechaActual(), 
        $request["solicitante"], $request["id_pendiente"]]);
        // $this->fileUpload($request, $request["id_pendiente"]);
        $descripcion = 'El usuario '.$request['usuario'].' ha modificado el pendiente '.$request["titulo"];
        $id_movimiento = $this->guardarMovimiento('editar', 1, $descripcion);
        return response()->json(['data'=>"El elemento se ha editado correctamente",'id_pendiente'=> $request["id_pendiente"],'id_movimiento'=>$id_movimiento, 'ok'=>true], 200);
    }

    public function getPendientesPaginado($index){
        // verificar pendientes vencidos
        $this->actualizaCompromisosVencidos();
        $this->actualizaPendientes();
        $data=DB::table('pendiente as p')
        ->join('cat_clasificacion as c', 'p.id_clasificacion', '=', 'c.id_clasificacion')
        ->join('cat_status as s', 's.id_status', '=', 'p.id_status')
        ->select('p.id_pendiente', 'p.id_status', 'p.id_clasificacion', 'p.prioridad','p.tipo_pendiente as pendiente', 'p.titulo', 'p.descripcion', 'p.fecha', 'p.archivo', 
        'p.num_compromisos', 'p.solicitante', 'p.tipo_pendiente','c.descripcion as clasificacion', 's.descripcion as status')
        ->take(6)
        ->skip($index)
        ->where('activo', true)
        ->get();

        foreach($data as $midata){
            if($midata->pendiente == 2){
                $midata->pendiente = "INTERNO";
            }else if($midata->pendiente == 1){
                $midata->pendiente = "EXTERNO";
            }
        }
        return $this->crearRespuesta($data, 200);
    }

    public function busquedaPorNombre(Request $request){
        try {    
            $busqueda = $request["buscador"];
            $data=DB::table('pendiente as p')
            ->join('cat_clasificacion as c', 'p.id_clasificacion', '=', 'c.id_clasificacion')
            ->join('cat_status as s', 's.id_status', '=', 'p.id_status')
            ->select('p.id_pendiente', 'p.id_status', 'p.id_clasificacion', 'p.prioridad','p.tipo_pendiente as pendiente', 'p.titulo', 'p.descripcion', 'p.fecha', 'p.archivo', 
            'p.num_compromisos', 'p.solicitante', 'p.tipo_pendiente','c.descripcion as clasificacion', 's.descripcion as status')
            ->orWhere('p.solicitante', 'LIKE', '%'.$busqueda.'%')
            ->take(15)
            ->get();
            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("Ha ocurrido un problema ". $th->getMessage(). ' '.$th->getLine(), 500);
        }
    }
    public function getInfoPendienteCompleto($id_pendiente){
        $data=DB::table('pendiente as p')
        ->join('cat_clasificacion as c', 'p.id_clasificacion', '=', 'c.id_clasificacion')
        ->join('cat_status as s', 's.id_status', '=', 'p.id_status')
        ->select('p.id_pendiente', 'p.id_status', 'p.id_clasificacion', 'p.prioridad','p.tipo_pendiente as pendiente', 'p.titulo', 'p.descripcion', 'p.fecha', 'p.archivo', 
        'p.num_compromisos', 'p.solicitante', 'p.tipo_pendiente','c.descripcion as clasificacion', 's.descripcion as status')
        ->where('p.activo', true)
        ->where('p.id_pendiente', $id_pendiente)
        ->first();

        if(!$data == null){
        if($data->pendiente == 2){
                $data->pendiente = "INTERNO";
            }else if($data->pendiente == 1){
                $data->pendiente = "EXTERNO";
            }
        }

        
            $compromisos = DB::table('compromiso as c')
            ->join('cat_status as s', 's.id_status', '=', 'c.id_status')
            ->select('c.*', 's.descripcion')
            ->where('c.id_pendiente', $id_pendiente)
            ->where('activo', true)
            ->get();
        return response()->json(['data'=>$data, "compromisos"=>$compromisos, 'ok'=>true], 200);
    }
    public function bajaPendiente(Request $request){
        DB::update('update pendiente 
        set activo = ?, fecha_modificacion= ?,
        usuario_modificacion = ?, num_compromisos = ?
        where id_pendiente = ?', 
        [false, $this->getHoraFechaActual(), 
        $request["usuario"], 0, $request["id_pendiente"]]);

        DB::update('update compromiso set activo = ?,
        fecha_modificacion= ?, usuario_modificacion = ? 
         where id_pendiente = ?', 
         [false, $this->getHoraFechaActual(), 
         $request["usuario"], 0, $request["id_pendiente"]]);
         $descripcion = 'El usuario '.$request['usuario'].' ha eliminado el pendiente '.$request["id_pendiente"];
         $id_movimiento = $this->guardarMovimiento('eliminar', 1, $descripcion);
         return response()->json(['data'=>"El elemento se ha creado correctamente", 'id_movimiento'=>$id_movimiento, 'ok'=>true], 200);
    }
    public function guardarObservacion(Request $request){
        DB::insert('insert into observaciones_pendientes 
        (id_pendiente, id_movimiento, descripcion, consecutivo, activo,
        usuario_creacion, usuario_modificacion, fecha_creacion, 
        fecha_modificacion) 
        values (?,?,?,?,?,?,?,?,?)', 
        [$request["id_pendiente"], $request["id_movimiento"],
        $request["descripcion"], 0, 
        true, $request["usuario"], $request["usuario"], 
        $this->getHoraFechaActual(), $this->getHoraFechaActual()]);
        return $this->crearRespuesta("Pendiente agregada correctamente", 200);

    }
    public function getByEstatus(Request $request){

        $miData=$request->all();
        $nombre = $miData["nombre"];
        $id_status = $miData["id_status"];
        $data;
        if(strlen($nombre) == 0 && $id_status > 0){
            $data=DB::table('pendiente as p')
            ->join('cat_clasificacion as c', 'p.id_clasificacion', '=', 'c.id_clasificacion')
            ->join('cat_status as s', 's.id_status', '=', 'p.id_status')
            ->select('p.id_pendiente', 'p.id_status','p.id_clasificacion', 'p.prioridad','p.tipo_pendiente as pendiente', 'p.titulo', 'p.descripcion', 'p.fecha', 'p.archivo', 
            'p.num_compromisos', 'p.solicitante', 'p.tipo_pendiente','c.descripcion as clasificacion', 's.descripcion as status')
            ->where('p.activo', true)
            ->where('p.id_status', $id_status)
            ->get();
        } else if($id_status == 0 && strlen($nombre) > 0){
            $data=DB::table('pendiente as p')
            ->join('cat_clasificacion as c', 'p.id_clasificacion', '=', 'c.id_clasificacion')
            ->join('cat_status as s', 's.id_status', '=', 'p.id_status')
            ->select('p.id_pendiente', 'p.id_status','p.id_clasificacion', 'p.prioridad','p.tipo_pendiente as pendiente', 'p.titulo', 'p.descripcion', 'p.fecha', 'p.archivo', 
            'p.num_compromisos', 'p.solicitante', 'p.tipo_pendiente','c.descripcion as clasificacion', 's.descripcion as status')
            ->where('p.activo', true)
            ->Where('p.solicitante', 'LIKE', '%'.$nombre.'%')
            ->get();
        } else if($id_status > 0 && strlen($nombre) > 0){
            $data=DB::table('pendiente as p')
            ->join('cat_clasificacion as c', 'p.id_clasificacion', '=', 'c.id_clasificacion')
            ->join('cat_status as s', 's.id_status', '=', 'p.id_status')
            ->select('p.id_pendiente', 'p.id_status','p.id_clasificacion', 'p.prioridad','p.tipo_pendiente as pendiente', 'p.titulo', 'p.descripcion', 'p.fecha', 'p.archivo', 
            'p.num_compromisos', 'p.solicitante', 'p.tipo_pendiente','c.descripcion as clasificacion', 's.descripcion as status')
            ->where('p.activo', true)
            ->Where('p.solicitante', 'LIKE', '%'.$nombre.'%')
            ->where('p.id_status', $id_status)
            ->get();
        }
        return $this->crearRespuesta($data, 200);
    }
    public function getPendientesExcel(){
        try {
            return Excel::download(new EmpresasExport, 'pendientes.xlsx');
        } catch (\Throwable $th) {
            return $this->crearRespuestaError("Ha ocurrido un problema ". $th->getMessage(). ' '.$th->getLine(), 500);
        }
    }

    
}