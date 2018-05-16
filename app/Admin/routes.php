<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    #设备类型
    $router->resource('equtypes', EquTypeController::class);
    #客户管理
    $router->resource('clients', ClientController::class);
    #光源管理
    $router->resource('equipments', EquipmentController::class);
});
