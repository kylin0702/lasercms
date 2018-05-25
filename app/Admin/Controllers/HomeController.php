<?php

namespace App\Admin\Controllers;

use App\admin\Models\Abnorma;
use App\Admin\Models\Client;
use App\Admin\Models\Equipment;
use App\Http\Controllers\Controller;
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
            $content->row(function(Row $row){
                $row->column(3,function (Column $column){
                    $model=new Client();
                    $count=$model->count();
                    $box=new InfoBox("客户数量","user","info","/admin/clients","$count");
                    $column->append($box);
                });
                $row->column(3,function (Column $column){
                    $model=new Equipment();
                    $count=$model->count();
                    $box=new InfoBox("光源数量","camera-retro","warning","/admin/equipments","$count");
                    $column->append($box);
                });
                $row->column(3,function (Column $column){
                    $model=new Abnorma();
                    $count=$model->count();
                    $box=new InfoBox("异常数量","bolt","danger","/admin/abnormas","$count");
                    $column->append($box);
                });
                $row->column(3,function (Column $column){
                    $model=new Equipment();
                    $count=$model->where('RemainTime','<','500')->count();
                    $box=new InfoBox("小于500小时","clock-o","success","/admin/equipments","$count");
                    $column->append($box);
                });
            });
            $content->row($this->abnormaGrid());
            $content->row($this->equGrid());
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
            $grid->Livephotos("现场图片1");
            $grid->Livephotos2("现场图片1");
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
            $grid->EquStatus('光源状态')->display(function($v){
                $status=["LampOn"=>"正在放映","Standby"=>"待机中","UnActive"=>"未激活"];
                if($v=="LampOn"){
                    return "<label class='label label-success'>$status[$v]</label> <i class='fa fa-cog fa-spin'></i>";
                }
                elseif ($v="Standby"){
                    return "<label class='label label-primary'>$status[$v]</label>";
                }
                elseif($v=="UnActive"){
                    return "<label class='label label-danger'>$status[$v]</label>";
                }
                else{
                    return "<label class='label label-default'>$status[$v]</label>";
                }
            });
        });
    }
    public function welcome(){
        return view("admin.index");
    }
}
