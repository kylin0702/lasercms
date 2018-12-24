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
    $router->get('clients/{ID}/audit', "ClientController@audit");//客户审核
    $router->post('clients/{ID}/bindEngineer', "ClientController@bindEngineer");//客户绑定工程师
    $router->post('clients/{ID}/bindAgent', "ClientController@bindAgent");//客户绑定代理商
    $router->post('clients/{ID}/bindSeller', "ClientController@bindSeller");//客户绑定销售人员
    $router->resource('clients', ClientController::class);
    #光源管理路由
    $router->get('equipments/getRoom', "EquipmentController@getRoom");//通过客户ID获取厅号
    $router->get('equipments/getEquipment', "EquipmentController@getEquipment");//通过客户ID获取拥有的光源信息
    $router->get('equipments/changeEquipment', "EquipmentController@changeEquipment");//更换光源编号
    $router->get('equipments/sms', "EquipmentController@sms");//发送删除光源验证码
    $router->resource('equipments', EquipmentController::class);
    $router->get('equipments/{clientid}/clientshow', "EquipmentController@clientshow");//按ClientID显示光源
    $router->post('equipments/{ID}/bind', "EquipmentController@bind");//绑定光源到客户
    $router->post('equipments/{ID}/unbind', "EquipmentController@unbind");//解绑光源
    $router->post('equipments/{ID}/lock', "EquipmentController@lock");//锁定光源
    $router->post('equipments/{ID}/unlock', "EquipmentController@unlock");//启用光源
    #光源状态路由
    $router->get('equstatuss/getStatus', "EquStatusController@getStatus");//获取状态数据
    $router->get('equstatuss/getShock', "EquStatusController@getShock");//获取振幕数据
    $router->get('equstatuss/exportExcel', "EquStatusController@exportExcel");
    $router->resource('equstatuss', EquStatusController::class);
    $router->get('equstatuss/{sn}/show', "EquStatusController@show");
    #区域设置路由
    $router->get('areas/getSonArea', "AreaController@getSonArea");//通过大区ID获取子区域
    $router->resource('areas', AreaController::class);
    #充值记录
    $router->get('recharges/batchCreate', "RechargeController@batchCreate");
    $router->get('recharges/sms', "RechargeController@sms");
    $router->post('recharges/batchRecharge', "RechargeController@batchRecharge");
    $router->get('recharges/getRecharge', "RechargeController@getRecharge");
    $router->get('recharges/exportExcel', "RechargeController@exportExcel");
    $router->resource('recharges', RechargeController::class);
    #异常记录
    $router->resource('abnormas', AbnormaController::class);
    #统计报表
    $router->get('durt/query/{ID}', "YearDurtReptController@query");//日结算明细
    $router->get('durt/exportExcel', "YearDurtReptController@exportExcel");//导出EXCEL
    $router->resource('durt', YearDurtReptController::class);
    $router->resource('cost', YearCostReptController::class);
    #日结算
    $router->get('balances/date', "DateBalanceController@autocreate");
    $router->get('balances/month_excel', "DateBalanceController@month_excel");//月结算excel表

});

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
], function (Router $router) {
    $router->get('balances/date', "DateBalanceController@autocreate");
    $router->get('balances/date_all', "DateBalanceController@autocreate_all");
});
