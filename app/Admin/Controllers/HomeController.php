<?php

namespace App\Admin\Controllers;

use App\admin\Models\Abnorma;
use App\Admin\Models\Client;
use App\Admin\Models\Equipment;
use App\Http\Controllers\Controller;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Callout;
use Encore\Admin\Widgets\InfoBox;
use function foo\func;
use Mockery\Matcher\Closure;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('信息面板');
            $content->description('');
            //超级管理员显示内容
            if(Admin::user()->inRoles(['administrator','manager'])) {
                $content->row(function (Row $row) {
                    $row->column(3, function (Column $column) {
                        $model = new Client();
                        $count = $model->count();
                        $box = new InfoBox("客户数量", "user", "info", "/admin/clients", "$count");
                        $column->append($box);
                    });
                    $row->column(3, function (Column $column) {
                        $model = new Equipment();
                        $count = $model->count();
                        $box = new InfoBox("光源数量", "camera-retro", "warning", "/admin/equipments", "$count");
                        $column->append($box);
                    });
                    $row->column(3, function (Column $column) {
                        $model = new Abnorma();
                        $count = $model->count();
                        $box = new InfoBox("异常数量", "bolt", "danger", "/admin/abnormas", "$count");
                        $column->append($box);
                    });
                    $row->column(3, function (Column $column) {
                        $model = new Equipment();
                        $count = $model->where('RemainTime', '<', '500')->count();
                        $box = new InfoBox("小于500小时", "clock-o", "success", "/admin/equipments", "$count");
                        $column->append($box);
                    });
                });
                $content->row(function (Row $row) {
                    $row->column(6, function (Column $column) {
                        $column->append($this->abnormaGrid());
                        $column->append($this->clientGrid());
                    });
                    $row->column(6, function (Column $column) {
                        $column->append($this->equGrid());
                    });
                });
            }
            //客户显示内容
            elseif (Admin::user()->inRoles(['client'])){
                $content->row(function (Row $row) {
                    $row->column(12, function (Column $column) {
                        $column->append($this->equGridForClient());
                    });
                });
            }
            //工程师显示内容
            elseif (Admin::user()->inRoles(['engineer'])){
                $content->row(function (Row $row) {
                    $row->column(12, function (Column $column) {
                        $column->append($this->equGridForEngineer());
                    });
                });
            }
            //代理商显示内容
            elseif (Admin::user()->inRoles(['agent'])){
                $content->row(function (Row $row) {
                    $row->column(12, function (Column $column) {
                        $column->append($this->equGridForAgent());
                    });
                });
            }
        });
    }
    protected function abnormaGrid()
    {
        return Admin::grid(Abnorma::class, function (Grid $grid) {
            $grid->disablePagination()->disableCreateButton()->disableExport()->disableRowSelector()->disableActions()->disableFilter();
            $grid->tools->disableBatchActions();
            $grid->tools->disableRefreshButton();
            $grid->tools->append("<label class='label label-danger'><i class='fa fa-spin fa-cogs'></i> 最新异常</label>");
            $grid->disableRowSelector();
            $grid->hasOneClient()->ClientName("客户名称");
            $grid->hasOneClient()->Owner("联系人");
            $grid->hasOneClient()->Phone("联系方式");
            $grid->hasOneEquipment()->NumBer("厅号");
            $grid->ProDesc("故障现象");
            $grid->Serious("严重程度");
            $grid->UpdateTime("登记时间");
        });
    }

    protected function equGrid()
    {
        return Admin::grid(Equipment::class, function (Grid $grid) {
            $grid->disablePagination()->disableCreateButton()->disableExport()->disableRowSelector()->disableActions()->disableFilter();
            $grid->tools->disableBatchActions();
            $grid->tools->disableRefreshButton();
            $grid->tools->append(" <label class='label label-warning'><i class='fa fa-spin fa-clock-o'></i> 剩余时间小于500小时</label>");
            $grid->disableRowSelector();
            $grid->model()->where("Remaintime","<=",500)->orderby("RemainTime");
            $grid->hasOneClient()->ClientName('客户名称');
            $grid->hasOneClient()->Owner('联系人');
            $grid->hasOneClient()->Phone('联系方式');
            $grid->NumBer('影厅号');
            $grid->RemainTime('剩余时长')->display(function ($v){return "<i class='fa fa-clock-o'></i> ".$v."小时";});
        });
    }

    protected function clientGrid()
    {
        return Admin::grid(Client::class, function (Grid $grid) {
            $grid->disablePagination()->disableCreateButton()->disableExport()->disableRowSelector()->disableActions()->disableFilter();
            $grid->tools->disableBatchActions();
            $grid->tools->disableRefreshButton();
            $grid->tools->append(" <label class='label label-success'><i class='fa fa-spin fa-star'></i> 最新客户</label>");
            $grid->disableRowSelector();
            $grid->model()->orderby("ID","desc")->take(10);
            $grid->ClientName('影城名称');
            $grid->VideoNum('影厅数量');
            $grid->Owner('影城法人');
            $grid->Phone('联系方式');
            $grid->UpdateTime('合作时间')->display(function ($v){
                return date("Y-m-d",strtotime($v));
            });

        });
    }

    protected function equGridForClient()
    {
        return Admin::grid(Equipment::class, function (Grid $grid) {
            $phone=Admin::user()->username;//用户登陆帐号为手机号码
            $client=Client::where("Phone","=",$phone)->get();
            $ids=[];
            foreach ($client as $v){
                array_push($ids,$v->ID);
            }
            $grid->model()->whereIn("ClientID",$ids);
            $grid->disablePagination()->disableCreateButton()->disableExport()->disableRowSelector()->disableActions()->disableFilter();
            $grid->tools->disableBatchActions();
            $grid->tools->disableRefreshButton();
            $grid->tools->append(" <i class='fa fa-camera'></i> 光源信息");
            $grid->disableRowSelector();
            $grid->hasOneClient()->ClientName("影城名称");
            $grid->NumBer('影厅号');
            $grid->hasOneEquType()->Name('光源型号');
            $grid->EquStatus("光源状态")->display(function($v){
                $reviewtime=strtotime($this->ReviewTime);
                $now=time();
                //4分钟未通讯显示离线
               if(($reviewtime+240)<$now){
                   return "<label class='label label-default'>离线</label>";
               }
               else{
                    $styles=["Standby"=>"label-info","LampOn"=>"label-success","UnActive"=>"label-warning"];
                    $names=["Standby"=>"待机中","LampOn"=>"放映中","UnActive"=>"未激活"];
                    return "<label class='label $styles[$v]'>$names[$v]</label>";
                }
            });
            $grid->RemainTime('剩余时长')->display(function ($v){return "<i class='fa fa-clock-o'></i> ".$v."小时";});
        });
    }
    protected function equGridForEngineer()
    {
            $username=Admin::user()->username;
            $clients=Client::with(['hasOneArea','hasOneEngineer'])
                ->where("engineer","=",$username)
                ->where("ClientName","like",'%'.request("name").'%')
                ->where("Phone","like",'%'.request("phone").'%')
                ->paginate(10);
            return view("admin.engineer",["clients"=>$clients]);
    }
    protected function equGridForAgent()
    {
        $username=Admin::user()->username;
        $clients=Client::with(['hasOneArea','hasOneEngineer'])
            ->where("agent","=",$username)
            ->where("ClientName","like",'%'.request("name").'%')
            ->where("Phone","like",'%'.request("phone").'%')
            ->paginate(10);
        return view("admin.agent",["clients"=>$clients]);
    }


}
