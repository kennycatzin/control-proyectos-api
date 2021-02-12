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
    $router->post('login', 'UserController@login');
    $router->get('profile', 'UserController@profile');
    $router->get('users/{id}', 'UserController@singleUser');
    $router->get('users', 'UserController@allUsers');
    

    $router->group(['prefix' => 'empresa'], function () use ($router) {
        $router->post('store-empresa', 'EmpresaController@storeEmpresa');     
        $router->get('get-listado-empresa/{index}', 'EmpresaController@getListadoEmpresas');   
        $router->get('get-detalle-empresa/{id_empresa}', 'EmpresaController@getDetalleEmpresa');
        $router->put('baja-empresa', 'EmpresaController@bajaEmpresa');  
        $router->post('busqueda', 'EmpresaController@busquedaPorNombre'); 
        $router->get('get-item-empresa/{id_empresa}', 'EmpresaController@getEmpresaItem');   
        $router->get('get-empresas-excel', 'EmpresaController@getEmpresasExcel');   

        
    });
});


