<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use DB;
class UserController extends Controller
{
     /**
     * Instantiate a new UserController instance.
     *
     * @return void
     */
    //public function __construct()
    //{
      //  $this->middleware('auth');
    //}

    /**
     * Get the authenticated User.
     *
     * @return Response
     */
    public function register(Request $request)
    {
       
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string|unique:users',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);

        try {
            // $user = new User;
            // $user->name = $request->input('name');
            // $user->email = $request->input('email');
            // $plainPassword = $request->input('password');
            // $user->password = app('hash')->make($plainPassword);
            // $user->save();
            DB::insert('insert into users (name, email, password, activo,
            fecha_creacion, fecha_modificacion, usuario_creacion, usuario_modificacion)
            values (?,?,?,?,?,?,?,?)', 
            [$request->input('name'), $request->input('email'), 
            app('hash')->make($request->input('password')), true, $this->getHoraFechaActual(), 
            $this->getHoraFechaActual(), $request->input('name'), $request->input('name')]);

            //return successful response
            return response()->json(['ok' => true, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed! '. $e->getMessage().' '.$e->getLine()], 200);
        }

    }
    public function login(Request $request)
    {
          //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['name', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized', 'ok' => false], 401);
        }
        return $this->respondWithToken($token);
    }

    /**
     * Get all User.
     *
     * @return Response
     */
    public function allUsers()
    {
         return response()->json(['users' =>  User::all()], 200);
    }

    /**
     * Get one user.
     *
     * @return Response
     */
    public function singleUser($id)
    {
        try {
            $user = User::findOrFail($id);

            return response()->json(['user' => $user], 200);

        } catch (\Exception $e) {

            return response()->json(['message' => 'user not found!'], 404);
        }

    }
    public function getUbicacionUsuario($id_usuario){
        $data=DB::table('cat_usuario')
        ->select('id_usuario')
        ->where('id_usuario', $id_usuario)
        ->first();

        if($data){
            return response()->json(['usuario' => 'pgty', 'ok'=>true], 200);
        }else{
            return response()->json(['usuario' => 'rupay', 'ok'=>true], 200);

        }
    }

    public function statusTramites($id){
        $query = DB::table('users')
        ->join('persona_tramite','persona_tramite.persona_id','=','users.id')
        ->join('procesos','persona_tramite.tramite_id','=','procesos.id_proceso')
        ->select('procesos.nombre','persona_tramite.status','persona_tramite.tramite_id')
        ->where('users.id',$id)
        ->get();

        if(count($query) >=1){  //
            return response()->json([
                'data' =>$query
            ]);
        }
        return false;
    }


    public function storeUsuario(Request $request){
        $reglas = [
            'nombre' => 'required',
            'email' => 'required',
            'rfc_o_curp' => 'required'
        ];
        $data=DB::table('cat_usuario')
        ->select('id_usuario')
        ->where('rfc_o_curp', $request->get('rfc_o_curp'))
        ->first();
        if($data){
            return response()->json(['data'=>['mensaje'=>'Usuario existente', 'id_usuario'=>$data->id_usuario], 'ok'=>true], 201); 
        }else{
            $fecha=$this->getHoraFechaActual();
            $id_usuario=$this->getSigUsuario();
            $this->validate($request, $reglas);
            DB::insert('insert into cat_usuario (id_usuario, nombre, rfc_o_curp, email, fecha_creacion, fecha_modificacion,
            usuario_creacion, usuario_modificacion, activo) values (?,?,?,?,?,?,?,?,?)',[ 
            $id_usuario, $request->get('nombre'), $request->get('rfc_o_curp'), $request->get('email'),
            $fecha, $fecha, 1, 1, 1]);
            return response()->json(['data'=>['mensaje'=>'Usuario guardado', 'id_usuario'=>$id_usuario], 'ok'=>true], 201);
        }
        
    }
    protected function getSigUsuario(){
        $data = DB::table('cat_usuario')
        ->select('id_usuario')
        ->orderBy('id_usuario', 'DESC')->first();
        $siguienteID=0;
        if($data){
            $siguienteID=$data->id_usuario+1;
        }else{
            $siguienteID=1;
        }
        return $siguienteID;
    }
    public function statusTramite($id_user,$id_tramite){
        $query = DB::table('users')
        ->join('persona_tramite','persona_tramite.persona_id','=','users.id')
        ->join('procesos','persona_tramite.tramite_id','=','procesos.id_proceso')
        ->select('procesos.nombre','persona_tramite.status','persona_tramite.tramite_id')
        ->where('users.id',$id_user)
        ->where('procesos.id_proceso',$id_tramite)
        ->get();

        if(count($query) >= 1){ //Existe ir a su tramite
            return response()->json([
                'data' =>$query
            ]);
        }else{
            return "false";
        }
        
    }
    public function ordenUserTramit($id_tramite, $id_user){
        $query = DB::table('persona_tramite')
        ->join('etapa_tramite_persona as etp','etp.persona_tramite_id','=','persona_tramite.id')
        ->join('etapa_tramite','etp.etapa_tramite_id','=','etapa_tramite.tramite_id')
        ->join('etapas','etapa_tramite.etapa_id','=','etapas.id')
        ->select('etapas.nombre')
        ->where('persona_tramite.persona_id',$id_user)
        ->where('persona_tramite.tramite_id',$id_tramite)
        ->orderby('etapa_tramite.orden','ASC')
        ->get();

        return response()->json([
            'data' =>$query
        ]);
    }
    public function storeDatosDependencia(Request $request){
        $this->validate($request, [
            'correo' => 'required',
            'id_dependencia' => 'required',
            'usuario' => 'required'
        ]);
        try {           
            DB::insert('insert into directorio_dependencia 
            (telefono, correo, nombre, titular, id_dependencia, activo,
            fecha_creacion, fecha_modificacion, usuario_creacion, usuario_modificacion)
            values (?,?,?,?,?,?,?,?,?,?)', 
            [$request["telefono"], $request["correo"], $request["nombre"], $request["titular"],
            $request["id_dependencia"], true, $this->getHoraFechaActual(), $this->getHoraFechaActual(),
            $request["usuario"], $request["usuario"]]);

            return $this->crearRespuesta("Registro almacenado", 200);

        } catch (\Throwable $th) {
            return $this->guardarLogErrores(null, null, null, null, __FUNCTION__, $th->getMessage());
        }
    }
    public function getDatosDependencia(){
        $data = DB::table('directorio_dependencia')
        ->select('*')
        ->where('activo', true)
        ->orderBy('id_dependencia', 'ASC')
        ->get();
        return $this->crearRespuesta($data, 200);
    }
    public function getDatoDependenciaID($id_dato){
        $data = DB::table('directorio_dependencia')
        ->select('*')
        ->where('activo', true)
        ->where('id_directorio_dependencia', $id_dato)
        ->orderBy('id_dependencia', 'ASC')
        ->get();
        return $this->crearRespuesta($data, 200);
    }
    public function updateDatosDependencia(Request $request){
        $this->validate($request, [
            'telefono' => 'required',
            'nombre' => 'required',
            'correo' => 'required',
            'titular' => 'required',
            'usuario' => 'required',
            'id_directorio_dependencia' => 'required',
            'id_dependencia' => 'required'
        ]);
        DB::update('update directorio_dependencia set 
        telefono = ?, correo = ?, nombre = ?, titular = ?, id_dependencia  = ?,
        usuario_modificacion = ?, fecha_modificacion = ?
         where id_directorio_dependencia = ?', 
         [$request["telefono"], $request["correo"], $request["nombre"], $request["titular"],
         $request["id_dependencia"], $request["usuario"], $this->getHoraFechaActual(), $request["id_directorio_dependencia"]]);
         return $this->crearRespuesta("Elemento modificado", 201);
    }
    public function deleteDatoDependencia(Request $request){
        $this->validate($request, [
            'usuario_modificacion' => 'required',
            'id_directorio_dependencia' => 'required'
        ]);
        try {       
            DB::update('update directorio_dependencia set 
            activo = false, usuario_modificacion = ?, fecha_modificacion = ?
            where id_directorio_dependencia = ?', 
            [$request["usuario"], $this->getHoraFechaActual(), $request["id_directorio_dependencia"]]);
            return $this->crearRespuesta("Elemento eliminado", 201);
        } catch (\Throwable $th) {
            return $this->guardarLogErrores(null, null, null, null, __FUNCTION__, $th->getMessage());
        }
    }

    public function storeAtencionCiudadana(Request $request){
        $correo = '';
        $titular = '';
        $dependencia = '';
        $mensaje = '';
        $this->validate($request, [
            'nombres' => 'required',
            'apellido_paterno' => 'required',
            'correo' => 'required',
            'id_dependencia' => 'required'
        ]);
        
            $miAmbiente = $this->getEnv("AMBIENTE", "");
            if($miAmbiente != ""){
                $miAmbiente = "(".$this->getEnv("AMBIENTE", "").")";
            }
            try {
            
                DB::insert('insert into atencion_ciudadana
                (sNombre, sApellidos, sComentario, sCorreo, 
                sTelefono, iIdDependencia, iIdInspeccion, dtCreacion, dtModificacion) 
                values (?,?,?,?,?,?,?,?,?)',
                [$request["nombres"], $request["apellido_paterno"]." ".$request["apellido_materno"], 
                $request["comentario"], $request["correo"], $request["telefono"], $request["id_dependencia"],
                null, $this->getHoraFechaActual(), $this->getHoraFechaActual()]);

                $dependencia = $this->infoDependencia($request["id_dependencia"]);
                if($dependencia === ""){
                    $dependencia = DB::table('directorio_dependencia')
                        ->select('correo', 'titular')
                        ->where('activo', true)
                        ->where('id_dependencia', 0)
                        ->first();
                    $mensaje = "La dependencia con id: ".$request["id_dependencia"]." ".$request["nombre_dependencia"]. " no cuenta con una cuenta de correo configurada
                    por lo cual no es posible enviarle los correos generados del formulario de Atención Ciudadana.";
                    $correo= $dependencia->correo;
                    $titular= $dependencia->titular;
                }else{
                    $correo= $dependencia->correo;
                    $titular= $dependencia->titular;
                
                }
                $datosCorreo = [
                    'email' => $correo,
                    'nombre' => $titular,
                    'subject' => 'Atención ciudadana '.$miAmbiente
                ];
                $datosView = [
                    'nombre' => $request["nombres"].' '.$request["apellido_paterno"].' '.$request["apellido_materno"],
                    'telefono' => $request["telefono"],
                    'cuenta' => $correo,
                    "mensaje" => $mensaje,
                    'nota' => $request["comentario"]
                ];
                $this->enviarAtencionCiudadana($datosView, $datosCorreo);
                return $this->crearRespuesta("Registro guardado con éxito", 201);
            } catch (\Throwable $th) {
                return $this->guardarLogErrores(null, null, null, null, __FUNCTION__, $th->getMessage(). ' '. $th->getLine());
            }
       
    }
    public function getSolicitudesAtencionCiudadana(Request $request){
        try {     
            $desde = $request["index"];
            $dependencia =  $request["id_dependencia"];
            $registros = DB::table('atencion_ciudadana')
            ->select('id_atencion_ciudadana', 
            DB::raw("CONCAT('sNombres',' ','sApellidos') AS nombre"), 'iIdDependencia',
            'sCorreo', 'sTelefono' )
            ->where('iIdDependencia', $dependencia)
            ->count();
            $data = DB::table('atencion_ciudadana')
            ->select('id_atencion_ciudadana', 
            DB::raw("CONCAT('sNombres',' ','sApellidos') AS nombre"), 'iIdDependencia',
            'sCorreo', 'sTelefono' )
            ->where('iIdDependencia', $dependencia)
            ->skip($desde)
            ->take(5)
            ->get();
            return response()->json([
                'registros' => $registros,
                'data' => $data,
                'ok' => true
            ]);
        } catch (\Throwable $th) {
            return $this->guardarLogErrores(null, null, null, null, __FUNCTION__, $th->getMessage());
        }
    }
    public function getSolicitudAtencionCiudadanaID($id_solicitud){
        try {      
            $data = DB::table('atencion_ciudadana')
            ->select('id_atencion_ciudadana', DB::raw("CONCAT('sNombres',' ','sApellidos') AS nombre"), 'iIdDependencia',
            'sCorreo', 'sTelefono', 'sComentario' )
            ->where('id_atencion_ciudadana', $id_solicitud)
            ->get();
            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            return $this->guardarLogErrores(null, null, null, null, __FUNCTION__, $th->getMessage());
        }
    }
    public function obtenerPlataformas(){
        try {
            $data = DB::table('cat_plataforma')
            ->select('id_plataforma', 'nombre')
            ->where('activo', true)
            ->get();
            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            return $this->guardarLogErrores(null, null, null, null, __FUNCTION__, $th->getMessage());
        }
    }
    public function obtenerEtapas(){
        try {
            $data = DB::table('cat_etapa')
            ->select('id_etapa', 'nombre','descripcion','tipo', 'macroetapa')
            ->where('activo', true)
            ->get();
            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            return $this->guardarLogErrores(null, null, null, null, __FUNCTION__, $th->getMessage());
        }
    }
    public function obtenerTramites(){
        try {
            $data = DB::table('tramite')
            ->select('id_tramite', 'id_proceso', 'tipo', 'nombre')
            ->where('activo', true)
            ->get();
            foreach($data as $dato){
                if($dato->nombre == "" || $dato->nombre == null){
                    $dato->nombre = $this->getDataTramiteById($dato->id_tramite)["nombre"];
                }            
            }
            return $this->crearRespuesta($data, 200);
        } catch (\Throwable $th) {
            return $this->guardarLogErrores(null, null, null, null, __FUNCTION__, $th->getMessage());
        }
    }
}