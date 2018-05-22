<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Client;
use App\Admin\Models\Equipment;
use App\admin\Models\Recharge;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class RechargeController extends Controller
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

            $content->header('充值记录');
            $content->description('');

            $content->body($this->grid());
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
     * Create interface.
     *@param $cid
     * *@param $eid
     * @return Content
     */
    public function add($cid,$eid)
    {
        return Admin::content(function (Content $content) use($cid,$eid) {

            $content->header('充值');
            $content->description('');

            $content->body($this->form($cid,$eid));
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Recharge::class, function (Grid $grid) {
            $grid->disableRowSelector()->disableCreateButton()->disableActions();
            $grid->model()->orderby("UpdateTime","desc");
            $grid->SerialNumber("充值订单号");
            $grid->client()->ClientName("客户名称");
            $grid->equ()->NumBer("放映厅");
            $grid->equ()->EquNum("光源序列");
            $grid->Method("充值方式")->display(function($v){
                $method=[0=>"网上充值",1=>"系统赠送"];
                return $method[$v];
            });
            $grid->Amount("充值金额")->display(function ($v){return "<i class='fa fa-rmb'></i> ".$v;});
            $grid->IP("充值IP");
            $grid->UpdateTime("充值时间");
            $grid->Results("充值状态")->display(function ($v){
                $results=[0=>"未支付",1=>"已支付",2=>"系统赠送",3=>"取消"];
                $style=[0=>"danger",1=>"success",2=>"warning",3=>"default"];
                return "<label class='label label-$style[$v]'>$results[$v]</label>";
            });


        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($cid,$eid)
    {
        return Admin::form(Recharge::class, function (Form $form) use($cid,$eid) {
            //客户Model
            $client=Client::find($cid);
            //设备Model
            $equipment=Equipment::find($eid);
            $form->setTitle($client->ClientName);
            $form->hidden("ClientID")->value($cid);
            $form->hidden("EquID")->value($eid);
            $form->html("<span class='form-control no-border'>$equipment->NumBer</span>","厅号");
            $form->html("<span class='form-control no-border'>$equipment->EquNum</span>","设备序列号");

        });
    }

}
