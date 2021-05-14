<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Barryvdh\DomPDF\Facade as PDF;
use Mail;
use Carbon\Carbon;

class Controller extends BaseController
{
    protected function respondWithToken($token, $usuario)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'ok' => true,
            'usuario' => $usuario,
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
    public function crearRespuesta($datos, $codigo){
        return response()->json(['data'=>$datos, 'ok'=>true], $codigo);
    }
    public function crearRespuestaError($msg, $codigo){
        return response()->json(['message'=>$msg, 'ok'=>false], $codigo);
    }
    public function actualizaCompromisosVencidos(){
        $compVencidos = DB::table('compromiso')
        ->select('id_compromiso', 'fecha')
        ->where('activo', true)
        ->where('id_status', '<>', 3)
        ->where('id_status', '<>', 5)
        ->get();
        foreach($compVencidos as $venc){
            if($venc->fecha < $this->getHoraFechaActual()){
                DB::update('update compromiso set id_status = 3,
                fecha_modificacion = ? 
                where id_compromiso = ?', 
                [$this->getHoraFechaActual(), $venc->id_compromiso]);
            }
        }
        return true;
    }
    public function actualizaPendientes(){
        $pendientes = DB::table('pendiente')
        ->select('id_pendiente', 'id_status')
        ->where('activo', true)
        ->where('id_status', '<>', 2)
        ->where('id_status', '<>', 3)
        ->where('id_status', '<>', 5)
        ->get();

        foreach ($pendientes as $pendiente) {
            $compromiso = DB::table('compromiso')
            ->select('id_compromiso', 'id_pendiente', 'id_status', 'fecha')
            ->where('activo', true)
            ->where('id_pendiente', $pendiente->id_pendiente)
            ->orderBy('fecha', 'DESC')
            ->first();
            if($compromiso != null){
                if($compromiso->fecha < $this->getHoraFechaActual() ){
                    DB::update('update pendiente set 
                    id_status = 3, fecha_modificacion = ? 
                    where id_pendiente = ?', 
                    [$this->getHoraFechaActual(), $compromiso->id_pendiente]);
                }else{
                    DB::update('update pendiente set 
                    id_status = 4, fecha_modificacion = ?, 
                    fecha = ?
                    where id_pendiente = ?', 
                    [$this->getHoraFechaActual(), $compromiso->fecha, $compromiso->id_pendiente]);
                }
            }
        }
    }
    public function guardarMovimiento($movimiento, $tipo, $descripcion){
        DB::insert('insert into movimientos 
        (movimiento, tipo, descripcion,
        activo, fecha_creacion, fecha_modificacion, 
        usuario_creacion, usuario_modificacion) 
        values (?,?,?,?,?,?,?,?)', 
        [$movimiento, $tipo, $descripcion, true, 
        $this->getHoraFechaActual(), $this->getHoraFechaActual(), 
        'admin', 'admin']);
        $id_movimiento = DB::getPdo()->lastInsertId();
        return $id_movimiento;
    }

    public function getEnv($nombre){
        return env($nombre,"");
    }
    public function getHoraFechaActual(){
        $mytime = Carbon::now();
        return $mytime;
    }
    public function enviarCorreo($datosView, $datosCorreo){
        Mail::send('plantilla_correo', $datosView, function ($message) use ($datosCorreo) {
            $message->from("noreply@notificaciones.yucatan.gob.mx", 'Sitio de certificaciones BUPSY');
            $message->to($datosCorreo["email"], $datosCorreo['nombre']);
            $message->cc($datosCorreo["ayuda1"], $datosCorreo['ayuda2']);
            $message->subject($datosCorreo['subject']);
        });
    }
}
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
