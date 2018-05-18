<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    #设备类型路由
    $router->resource('equtypes', EquTypeController::class);
    #客户管理路由
    $router->resource('clients', ClientController::class);
    #光源管理路由
    $router->resource('equipments', EquipmentController::class);
    $router->get('equipments/{clientid}/clientshow', "EquipmentController@clientshow");//按ClientID显示光源
    #光源状态路由
    $router->resource('equstatuss', EquStatusController::class);
    $router->get('equstatuss/{sn}/show', "EquStatusController@show");
    #区域设置路由
    $router->resource('areas', AreaController::class);

});
