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
    $router->get('equipments/getRoom', "EquipmentController@getRoom");//通过客户ID获取厅号
    $router->resource('equipments', EquipmentController::class);
    $router->get('equipments/{clientid}/clientshow', "EquipmentController@clientshow");//按ClientID显示光源
    $router->post('equipments/{ID}/bind', "EquipmentController@bind");//绑定光源到客户
    $router->post('equipments/{ID}/unbind', "EquipmentController@unbind");//解绑光源
    #光源状态路由
    $router->resource('equstatuss', EquStatusController::class);
    $router->get('equstatuss/{sn}/show', "EquStatusController@show");
    #区域设置路由
    $router->get('areas/getSonArea', "AreaController@getSonArea");//通过大区ID获取子区域
    $router->resource('areas', AreaController::class);
    #充值记录
    $router->get('recharges/sms', "RechargeController@sms");
    $router->resource('recharges', RechargeController::class);
    #异常记录
    $router->resource('abnormas', AbnormaController::class);
    #统计报表
    $router->resource('durt', YearDurtReptController::class);
    $router->resource('cost', YearCostReptController::class);

});
