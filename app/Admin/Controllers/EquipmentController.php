<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Equipment;
use App\admin\Models\EquStatus;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use function foo\func;
use Illuminate\Support\Facades\Input;
use Symfony\Component\Console\Output\Output;

class EquipmentController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('光源管理');
            $content->description('光源信息列表');
            $content->body($this->grid());
        });
    }
    /**
     * Index interface.
     *
     * @return Content
     */
    public function clientshow($clientid)
    {
        return Admin::content(function (Content $content) use($clientid) {

            $content->header('光源管理');
            $content->description('光源信息列表');
            $content->body($this->clientgrid($clientid));
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Equipment::class, function (Grid $grid) {
              $grid->hasOneClient()->ClientNum('客户编号');
               $grid->hasOneClient()->ClientName('客户名称');
               $grid->NumBer('影厅号');
               $grid->hasOneEquType()->Name('光源类型');
                #$grid->EquType()->Price('单价');
               #$grid->EquType()->GiftTime('赠送时长');
               $grid->EquNum("设备编号")->display(function ($v) {
                  return $v;
            });
               $grid->RemainTime('剩余时长');
            $grid->EquStatus('光源状态')->display(function($v){
                $status=["LampOn"=>"正在放映","Standby"=>"待机中","UnActive"=>"未激活","Active"=>"关机"];
                if($v=="LampOn"){
                    return "<label class='label label-success'>$status[$v]</label> <i class='fa fa-cog fa-spin'></i>";
                }
                elseif ($v="Standby"){
                    return "<label class='label label-primary'>$status[$v]</label>";
                }
                elseif($v="UnActive"){
                    return "<label class='label label-danger'>$status[$v]</label>";
                }
                else{
                    return "<label class='label label-default'>$status[$v]</label>";
                }
            });
               $grid->Review('审核状态');

        });
    }

    protected function clientgrid($clientid)
    {
        return Admin::grid(Equipment::class, function (Grid $grid) use($clientid) {
            $user=Admin::user();

            $grid->model()->where("ClientID","=",$clientid);
            $grid->hasOneClient()->ClientNum('客户编号');
            $grid->hasOneClient()->ClientName('客户名称');
            $grid->NumBer('影厅号');
            $grid->hasOneEquType()->Name('光源类型');
            $grid->hasOneEquType()->Price('单价');
            $grid->hasOneEquType()->GiftTime('赠送时长');
            $grid->EquNum("设备编号")->display(function ($v) {
                return $v;
            });
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

            $grid->ISBuy('是否购买');
            $grid->ReviewTime('审核时间')->display(function($v){return date("Y-m-d H:i:s",strtotime($v)); });
            #$grid->Precharge('是否预充值');
            #$grid->PreGift('是否预赠送时长');
            # $grid->IsPre('是否已充充值成功');
            #$grid->IsSend('是否发送短信');
            #$grid->IsEnabled('是否启用');
            #$grid->IsDelay('延迟充值');

            //非权限内角色隐藏工作栏
            if(!$user->inRoles(['administrator'])) {
                $grid->disableActions()->disableCreateButton()->disableRowSelector()->disableFilter()->disableExport();
                $grid->tools->disableBatchActions();
                $grid->Review('审核状态');
            }
            //管理员权限
            if($user->inRoles(['administrator'])) {
                $grid->EntryPer('录入人');
                $grid->Auditor('是否审核');
                $grid->actions(function ($a) {
                    $a->disableDelete();
                    $a->disableEdit();
                    $cid=$a->row->ClientID;
                    $eid=$a->row->ID;
                    $href1="/admin/recharges/create?cid=$cid&eid=$eid&method=0";
                    $href2="/admin/recharges/create?cid=$cid&eid=$eid&method=1";
                    $a->append("<a href='$href1' class='btn btn-xs btn-warning'>充值 <i class='fa fa-rmb'></i></a> ");
                    $a->append("<a href='$href2' class='btn btn-xs btn-danger'>赠送 <i class='fa fa-gift'></i></a>");
                });
            }
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Equipment::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
