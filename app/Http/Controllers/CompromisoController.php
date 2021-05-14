<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Exports\EmpresasExport;
use Maatwebsite\Excel\Facades\Excel;
class CompromisoController extends Controller
{
   
   public function storeCompromiso(Request $request){
    DB::insert('insert into compromiso 
    (id_pendiente, id_status, compromiso, responsable, fecha, prioridad,
    activo, usuario_creacion, usuario_modificacion, fecha_creacion, fecha_modificacion) 
    values (?,?,?,?,?,?,?,?,?,?,?)',
     [$request["id_pendiente"], $request["id_status"], $request["compromiso"],
     $request["responsable"], $request["fecha"], $request["prioridad"],
     1, $request["usuario"], $request["usuario"],
    $this->getHoraFechaActual(), $this->getHoraFechaActual()]);
    $id_compromiso = DB::getPdo()->lastInsertId();
     // $this->fileUpload($request, $id_compromiso);

    $registros = DB::table('compromiso')
    ->select('id_compromiso')
    ->where('id_pendiente', $request["id_pendiente"])
    ->where('activo', true)
    ->count();

    DB::update('update pendiente 
    set  num_compromisos = ? where id_pendiente = ?', 
    [$registros, $request["id_pendiente"]]);
    $descripcion = 'El usuario '.$request['usuario'].' ha creado el compromiso '.$request["compromiso"];
    $id_movimiento = $this->guardarMovimiento('insertar', 2, $descripcion);
    return response()->json(['data'=>"El elemento se ha creado correctamente", 'id_compromiso'=> $id_compromiso,'id_movimiento'=>$id_movimiento, 'ok'=>true], 200);
   }
   public function fileUpload(Request $request, $id) {
     $response = null;
     $user = (object) ['archivo' => ""];
         if ($request->hasFile('archivo')) {
             $original_filename = $request->file('archivo')->getClientOriginalName();
             $original_filename_arr = explode('.', $original_filename);
             $file_ext = end($original_filename_arr);
             $destination_path = './upload/compromisos/';
             $image = 'C-' . $id . '.' . $file_ext;
             if ($request->file('archivo')->move($destination_path, $image)) {
                 // $user->image = './upload/pendientes/'.$image;
                 // $pasiente->imagen = $image;
                 $data = DB::table('compromiso')
                 ->select('archivo')
                 ->where('activo', true)
                 ->where('id_compromiso', $id)
                 ->first();
                 if(file_exists("upload/compromisos/".$data->archivo)) {
                     @unlink("upload/compromisos/".$data->archivo);
                 }
                 DB::update('update compromiso set archivo = ? where id_compromiso = ?', 
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
   public function actualizarCompromiso(Request $request){
    DB::update('update compromiso 
    set id_status = ?, compromiso = ?,  responsable = ?, fecha = ?, prioridad= ?,
    usuario_modificacion = ?, fecha_modificacion=  ? 
    where id_compromiso = ?', 
    [$request["id_status"], $request["compromiso"], $request["responsable"], $request["fecha"],
    $request["prioridad"], $request["usuario"], $this->getHoraFechaActual(),
    $request["id_compromiso"]]);
   //  $this->fileUpload($request, $request["id_compromiso"]);
    $descripcion = 'El usuario '.$request['usuario'].' ha modificado el compromiso '.$request["compromiso"];
    $id_movimiento = $this->guardarMovimiento('modificar', 2, $descripcion);
    return response()->json(['data'=>"El elemento se ha actualizado", 'id_movimiento'=>$id_movimiento, 'ok'=>true], 200);
   }
   public function bajaCompromiso(Request $request){
        DB::update('update compromiso 
        set activo = ?, usuario_modificacion = ? ,
        fecha_modificacion = ? 
        where id_compromiso = ?', 
        [false, 
        $request["usuario"], 
        $this->getHoraFechaActual(), 
        $request["id_compromiso"]]);

        $registros = DB::table('compromiso')
        ->select('id_compromiso')
        ->where('id_pendiente', $request["id_pendiente"])
        ->where('activo', true)
        ->count();

        DB::update('update pendiente 
        set  num_compromisos = ? where id_pendiente = ?', 
        [$registros, $request["id_pendiente"]]);

        $descripcion = 'El usuario '.$request['usuario'].' ha eliminado el compromiso '.$request["id_compromiso"];
        $id_movimiento = $this->guardarMovimiento('eliminar', 2, $descripcion);
        return response()->json(['data'=>"El elemento se ha eliminado correctamente", 'id_movimiento'=>$id_movimiento, 'ok'=>true], 200);
   }
   public function guardarObservacion(Request $request){
        DB::insert('insert into observaciones_compromisos 
        (id_compromiso, id_movimiento, descripcion, consecutivo, activo, usuario_creacion, usuario_modificacion,
        fecha_creacion, fecha_modificacion) 
        values (?,?,?,?,?,?,?,?,?)', 
        [$request["id_compromiso"], $request["id_movimiento"], $request["descripcion"], 1, true, $request["usuario"], 
        $request["usuario"], $this->getHoraFechaActual(), $this->getHoraFechaActual()]);
        return response()->json(['data'=>"Observación registrada", 'ok'=>true], 200);
    }
    public function getComprimisosPorPendiente(Request $request){
        $miData=$request->all();
        $id_pendiente = $miData["id_pendiente"];
        $nombre = $miData["nombre"];
        $id_status = $miData["id_status"];
        $data;
        if($id_pendiente > 0 && strlen($nombre) == 0 && $id_status == 0){
            $data = DB::table('compromiso as c')
            ->join('cat_status as s', 's.id_status', '=', 'c.id_status')
            ->join('pendiente as p', 'p.id_pendiente', '=', 'c.id_pendiente')
            ->select('c.*', 's.descripcion', 'p.titulo as pendiente')
            ->where('c.id_pendiente', $id_pendiente)
            ->where('c.activo', true)
            ->get();
        } elseif($id_pendiente > 0 && strlen($nombre) == 0 && $id_status > 0){

            $data = DB::table('compromiso as c')
            ->join('cat_status as s', 's.id_status', '=', 'c.id_status')
            ->join('pendiente as p', 'p.id_pendiente', '=', 'c.id_pendiente')
            ->select('c.*', 's.descripcion', 'p.titulo as pendiente')
            ->where('c.id_pendiente', $id_pendiente)
            ->where('c.id_status', $id_status)
            ->where('c.activo', true)
            ->get();
        } elseif($id_pendiente > 0 && strlen($nombre) > 0 && $id_status > 0){

            $data = DB::table('compromiso as c')
            ->join('cat_status as s', 's.id_status', '=', 'c.id_status')
            ->join('pendiente as p', 'p.id_pendiente', '=', 'c.id_pendiente')
            ->select('c.*', 's.descripcion', 'p.titulo as pendiente')
            ->where('c.id_pendiente', $id_pendiente)
            ->where('c.id_status', $id_status)
            ->Where('c.responsable', 'LIKE', '%'.$nombre.'%')
            ->where('c.activo', true)
            ->get();
        }elseif ($id_pendiente > 0 && strlen($nombre) > 0 && $id_status == 0){

            $data = DB::table('compromiso as c')
            ->join('cat_status as s', 's.id_status', '=', 'c.id_status')
            ->join('pendiente as p', 'p.id_pendiente', '=', 'c.id_pendiente')
            ->select('c.*', 's.descripcion', 'p.titulo as pendiente')
            ->where('c.id_pendiente', $id_pendiente)
            ->Where('c.responsable', 'LIKE', '%'.$nombre.'%')
            ->where('c.activo', true)
            ->get();
        }

 

          return $this->crearRespuesta($data, 200);
     }

    
}