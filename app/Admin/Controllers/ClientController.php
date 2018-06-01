<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Area;
use App\admin\Models\Client;
use App\admin\Models\Equipment;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Box;

class ClientController extends Controller
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

            $content->header('客户管理');
            $content->description('客户信息列表');
            $content->body($this->collapse());
            Admin::script(
                <<<EOT
            $(".panel").removeClass('box-primary').css("margin-bottom","10px");
            $(".panel-collapse").find(".box-header").addClass("hidden");
            $("[data-toggle='collapse']").on('click',function(){
                alert(1);
            });
EOT
            );
        });
    }

    /**
     * Make a CollapseGrid builder.
     *折叠样式数据
     * @return CollapseGrid
     */
    public  function  collapse(){
        $collapse = new Collapse();
        $clients=Client::all();

        foreach ($clients as $v){
            $clientid=$v->ID;
            $area=json_decode($v->hasOneArea)->AreaName;
            $updatetime=date("Y-m-d",strtotime($v->UpdateTime));
            $html=<<<EOT
<div class="row">
<input type="hidden" class="ClientID" value="$clientid">
<div class="col-lg-3">法人名称:$v->Owner</div>
<div class="col-lg-3">联系方式:$v->Phone</div>
<div class="col-lg-3">所属区域:$area</div>
<div class="col-lg-3">注册时间:$updatetime</div>
</div>
<div class="row">
<div class="col-lg-6">客户地址:$v->Adress</div>
<div class="col-lg-6">审核状态:$v->Review</div>
</div>
EOT;
            $box = new Box('客户详情', $html);
            //$header=["ID","NumBer"];
            //$rows=$equipment;
            //$table=new Table($header,$rows);
            $collapse->add($v->ClientName, $box);
        }
        return  $collapse->render();
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

            $content->header('修改客户信息');
            $content->description('');

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

            $content->header('添加客户信息');
            $content->description('');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *网格样式数据
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Client::class, function (Grid $grid) {
            /***禁用批量操作***/
            $grid->disableRowSelector();
            $grid->tools->disableBatchActions();

            $grid->ClientNum('客户编号');
            $grid->ClientName('影城名称');
            $grid->Adress('影城地址');
            #$grid->JoinHotline('加盟热线');
            #$grid->VideoNum('影厅数量');
            $grid->Owner('影城法人');
            $grid->Phone('联系方式');
            $grid->UpdateTime('合作时间')->display(function ($v){
                return date("Y-m-d",strtotime($v));
            });
            //$grid->area()->AreaCode("区域代码");
            //$grid->area()->AreaName("区域名称");

            $grid->Review('审核状态')->display(function ($v){
                if($v=="已审核"){
                    return "<label class='label label-success'>$v</label>";
                }
                else{
                    return "<label class='label label-warning'>$v</label>";
                }
            });
            $grid->username("绑定用户")->editable('text');
            $grid->hasOneAuditor()->name("审核人");
            $grid->Remark('备注');
            Admin::script("");
            $grid->actions(function ($actions) {
                $id=$actions->getKey();
                $actions->disableDelete();
                $actions->append("<a href='/admin/equipments/$id/clientshow'><i class='fa fa-camera-retro'></i></a>");
            });
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->equal('ClientName', '客户名称')->select(Client::all()->pluck('ClientName',"ClientName"));
                $filter->equal('AreaID', '区域')->select(Area::all()->pluck('AreaName',"ID"));
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
        return Admin::form(Client::class, function (Form $form) {
            $user=Admin::user();
            $method=request()->route()->getActionMethod();//获取路由方法,判断是增加还是修改

            $form->ignore(["Superior","MaxID"]);
            $form->hidden('ClientNum');
            $form->hidden("MaxID")->default(function() {
                return Client::max("ID");
            });
            $form->text('ClientName', '影城名称')->setWidth(5);
            $form->text('Adress', '影城地址');
            #$form->mobile('JoinHotline', '加盟热线');
            $form->number('VideoNum', '影厅数量');
            $form->text('Owner', '影城法人')->setWidth(2);
            $form->mobile('Phone', '联系方式');
            if($method=="create") {
                $form->date('UpdateTime', '合作时间');
            }
            else{
                $form->display('UpdateTime', '合作时间')->setWidth(2);
            }

            /****区域-省二级联动 Start****/
            $form->select('area.Superior', '父级区域')->setElementName("Superior")->options(function (){
                $data=[];
                $superior=Area::where("Superior","=",0)->get();
                foreach ($superior as $item){
                    $data[$item["ID"]]=$item["AreaName"];
                }
                return $data;
            })->load("AreaID","/admin/areas/getSonArea","ID","AreaName")->setWidth(2);
            $form->select('AreaID', '区域名称')->options(function($v){
                if(!empty($v)) {
                    $sid = Area::find($v)->Superior;
                    $data = [];
                    $superior = Area::where("Superior", "=", $sid)->get();
                    foreach ($superior as $item) {
                        $data[$item["ID"]] = $item["AreaCode"].$item["AreaName"];
                    }
                    return $data;
                }

            })->setWidth(2);
            /****区域-省二级联动 End****/

            /****让有审核权限的人进行审核****/
            if($user->inRoles(['administrator'])) {
                $states = [
                    'on' => ['value' => '已审核', 'text' => '已审核', 'color' => 'success'],
                    'off' => ['value' => '未审核', 'text' => '未审核', 'color' => 'danger'],
                ];
                $form->switch('Review', '审核状态')->states($states)->default("未审核");
            }

            $form->hidden('EntryPer')->default(function() use($user){
                return $user->id;
            });

            /******根据区域代码生成客户编号*******/
            Admin::script(
                <<<EOT
                $(".AreaID").on("change",function(){
                    var code="CN"+$(this).find("option:selected").text().substring(0,2)+"-";
                    var max=parseInt($('.MaxID').val())+1;
                    var clientnum=GenClientNum(code,max.toString());
                    $(".ClientNum").val(clientnum);
                });
                function GenClientNum(code,str) {  
                    var pad = "00000"  
                    return code+pad.substring(0, pad.length - str.length) + str  
                }  
EOT

            );
        });
    }

    protected function equipmentGrid($clientid)
    {
        return Admin::grid(Equipment::class, function (Grid $grid) use($clientid) {
            $user=Admin::user();
            $grid->model()->where("ClientID","=",$clientid);
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
                $grid->tools->disableBatchActions();
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
                    $a->append("<a href='$href2' class='btn btn-xs btn-danger'>赠送 <i class='fa fa-gift'></i></a> ");
                    $a->append("<button  class='btn btn-xs btn-yahoo unbind' data-eid='$eid' >解绑 <i class='fa fa-share-alt'></i></button>");
                });
                Admin::script(
                    <<<EOT
              $('.unbind').on('click',function(){
                var eid=$(this).attr('data-eid');
                if(confirm("是否解除光源与客户的绑定")){
                     $(this).find('i').addClass('fa-spin');
                     $.post('/admin/equipments/'+eid+'/unbind',{},function(data){
                          if(data.ClientID==0){
                                alert("解绑成功！");
                                window.location.reload();
                          }
                    });
                }              
                });
EOT
                );
            }
        });
    }
}
