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
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'ok' => true,
            'expires_in' => Auth::factory()->getTTL() * 60
        ], 200);
    }
    public function crearRespuesta($datos, $codigo){
        return response()->json(['data'=>$datos, 'ok'=>true], $codigo);
    }
    public function crearRespuestaError($msg, $codigo){
        return response()->json(['message'=>$msg, 'ok'=>false], $codigo);
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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
