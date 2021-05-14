<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return view("index");
});

$router->group(['prefix' => 'api'], function () use ($router) { 
    $router->post('register', 'UserController@register');
    $router->post('auth/login', 'UserController@login');

    $router->group(['prefix' => 'usuario'], function () use ($router) {
      $router->post('editar-usuario', 'UserController@editarUsuario');
      $router->post('busqueda', 'UserController@busquedaPorNombre');
      $router->get('get-usuarios/{index}', 'UserController@getUsuariosPaginado');
      $router->post('delete-usuario', 'UserController@deleteUsuario');
  });
    

    $router->group(['prefix' => 'pendiente'], function () use ($router) {
        $router->post('guardar-pendiente', 'PendienteController@storePendiente');  
        $router->post('subir-archivo/{id}', 'PendienteController@fileUpload');  
        $router->post('editar-pendiente', 'PendienteController@editarPendiente');    
        $router->post('guardar-observacion', 'PendienteController@guardarObservacion');     
        $router->get('get-pendientes/{index}', 'PendienteController@getPendientesPaginado');     
        $router->get('get-info-pendiente/{id_pendiente}', 'PendienteController@getInfoPendienteCompleto');
        $router->put('baja-pendiente', 'PendienteController@bajaPendiente');    
        $router->get('get-pendientes-excel', 'PendienteController@getPendientesExcel');   
        $router->post('busqueda', 'PendienteController@busquedaPorNombre'); 
        $router->post('get-por-estatus', 'PendienteController@getByEstatus');

        
    });

    $router->group(['prefix' => 'clasificacion'], function () use ($router) {
      $router->get('get-clasificaciones', 'ClasificacionController@getClasificaciones');
      $router->get('get-estatus', 'ClasificacionController@getEstatus');

           
    });
    $router->group(['prefix' => 'bitacora'], function () use ($router) {
        $router->get('get-bitacora-pendientes/{index}', 'BitacoraController@getBitacoraPendientes');
        $router->get('get-bitacora-compromisos/{index}', 'BitacoraController@getBitacoraCompromisos');
        $router->post('get-pendientes-dia', 'BitacoraController@getPendientesDia');
        $router->post('get-pendientes-periodo', 'BitacoraController@getPendientesPeriodo');
        $router->post('get-compromisos-dia', 'BitacoraController@getCompromisosDia');
        $router->post('get-compromisos-periodo', 'BitacoraController@getCompromisosPeriodo');
      });
    
    $router->group(['prefix' => 'compromiso'], function () use ($router) {
        $router->post('guardar-compromiso', 'CompromisoController@storeCompromiso'); 
        $router->post('subir-archivo/{id}', 'CompromisoController@fileUpload');   
        $router->post('editar-compromiso', 'CompromisoController@actualizarCompromiso');    
        $router->put('baja-compromiso', 'CompromisoController@bajaCompromiso');    
        $router->post('guardar-observacion', 'CompromisoController@guardarObservacion');    
        $router->post('get-compromisos-pendiente', 'CompromisoController@getComprimisosPorPendiente');   
    });
    
    $router->group(['prefix' => 'inicio'], function () use ($router) {
        $router->get('get-tablero/{tipo}', 'InicioController@getTablero');     
      });

    
    
});


