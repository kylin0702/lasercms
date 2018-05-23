<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Client;
use App\Admin\Models\Equipment;
use App\Admin\Models\EquType;
use App\admin\Models\Recharge;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use Symfony\Component\Debug\Debug;

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

            $content->header('光源充值');
            $content->description('');

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
            $grid->RechTime("充值小时数")->display(function ($v){return "<i class='fa fa-clock-o'></i> ".$v;});
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
    protected function form()
    {
        $cid=Input::get("cid");
        $eid=Input::get("eid");

        return Admin::form(Recharge::class, function (Form $form) use($cid,$eid) {
            //把cid,eid保存到form,因为store操作也是调用这个form,但url参数会被忽略掉
            $form->hidden("cid")->value($cid);
            $form->hidden("eid")->value($eid);
            $form->ignore(["cid","eid"]);//不参与数据库操作

            //客户Model
            $client=Client::findOrFail($cid);
            //设备Model
            $equipment=Equipment::findOrFail($eid);
            //设备类型
            $typeid=$equipment->EquTypeID;
            $equtype=EquType::findOrFail($typeid);
            $form->setTitle($client->ClientName);
            $form->hidden("ClientID")->value($cid);
            $form->hidden("EquID")->value($eid);
            $form->hidden("SerialNumber")->default(function(){
               return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            });
            $form->hidden("IP")->default(function (){
                return \Illuminate\Support\Facades\Request::ip();
            });
            $form->hidden("UpdateTime")->default(function (){
                return date("Y-m-d H:i:s.000");
            });
            $form->hidden("Results")->default(0);
            $form->hidden("AccountID")->default(0);
            $form->html("<span class='form-control no-border'>$equipment->NumBer</span>","厅号");
            $form->html("<span class='form-control no-border'>$equipment->EquNum</span>","光源序列号");
            $form->html("<span class='form-control no-border'>$equtype->Name</span>","光源类型");
            $form->html("<span class='form-control no-border'>$equtype->Price/小时</span>","光源单价");
            $form->hidden("UnitPrice")->default(function() use($equtype){
               return $equtype->Price;
            });
            $form->radio("Method","充值方式")->options([0 => '网上充值', 1=> '系统赠送'])->default(0);
            $form->text("RechTime","充值小时数")->default(0);
            $form->html("<span class='form-control no-border totle '>0元</span>","总计");
            $form->hidden("Amount")->default(0);
            Admin::script(
                <<<EOT
                $('#RechTime').on('input propertychange',function(){
                    var price=$('.UnitPrice').val()
                    var totle=$('#RechTime').val()*price;
                    $('.totle').html(totle+'元');
                    $('.Amount').val(totle);
                });      
EOT
            );
        });
    }

}
