<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Client;
use App\Admin\Models\Equipment;
use App\admin\Models\EquStatus;
use App\Admin\Models\EquType;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
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

            $content->header(Client::find($clientid)->ClientName);
            $content->description("光源列表");
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

            $content->header('添加光源');
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
            $token=$user->getRememberToken();

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
                $grid->disableCreateButton()->disableRowSelector();
                $grid->tools->disableBatchActions();

                //输出可以选择的光源
                $table=$this->getUnbindEqu($clientid);
                $scrip=<<<EOT
<button type="button" class='btn btn-sm btn-success' data-toggle="modal" data-target="#myModal" ><i class='fa fa-plus'></i> 绑定光源</button>
<!-- Modal -->
<div class="modal" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">选择要绑定的光源</h4>
      </div>
      <div class="modal-body">
        <div class="form-group">
        <label class=""><i class="fa  fa-th-large"></i>厅号: </label> <input type="text" class="form-control-plaintext" name="NumBer" placeholder="请输入厅号"/> 
        </div>
         $table
      </div>
       <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      </div>
    </div>
  </div>
</div>
EOT;
                $grid->tools->append($scrip);

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
                    $a->append("<a href='' class='btn btn-xs btn-yahoo'>解绑 <i class='fa fa-share-alt'></i></a>");
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
            $form->text("EquNum","光源编号")->setWidth(2)->rules("required",['required'=>'请输入光源编号']);
            $form->select("EquTypeID","光源类型")->options(function(){
                $data=[];
                $models=EquType::all();
                foreach ($models as $item){
                    $data[$item["ID"]]=$item["Name"]." |赠送时长:".$item["GiftTime"];
                }
                return $data;
            })->setWidth(2)->rules('required',['required'=>'请选择光源类型']);
            $form->hidden("GiftTime")->default(0);
            $form->hidden("EquStatus")->default("UnActive");
            $states = [
                'on' => ['value' => '已审核', 'text' => '已审核', 'color' => 'success'],
                'off' => ['value' => '未审核', 'text' => '未审核', 'color' => 'danger'],
            ];
            $form->switch('Review', '审核状态')->states($states)->default("未审核");
            $isbuy= [
                'on' => ['value' => '是', 'text' => '是', 'color' => 'success'],
                'off' => ['value' => '否', 'text' => '否', 'color' => 'danger'],
            ];
            $form->switch('IsBuy', '是否购买')->states($isbuy)->default("否");
            $form->hidden("EntryPer")->default(function (){
               return Admin::user()->ID;
            });
            /*
            $form->saving(function ($form) {
                $error = new MessageBag([
                    'title'   => '添加失败',
                    'message' => '光源编号已存在',
                ]);
                return back()->with(compact('error'));

            });*/
            //根据所选光源类型取得赠送时长
            Admin::script(
                <<<EOT
            $(".EquTypeID").on("change",function(){
                    var str=$(this).find("option:selected").text();
                    var index=str.indexOf(':');
                    var gift= str.substring(index+1);
                    $(".GiftTime").val(gift);
                });
EOT

            );
        });
    }
    public function getUnbindEqu($clientid){
       $unbinder=Equipment::where('ClientID','=',null)->leftJoin('EquType','Equipment.EquTypeID','=','EquType.ID')->get(['Equipment.ID','EquNum','EquType.Name','EquType.Price'])->toArray();
        $headers = ['ID','光源编号','光源类型','单价','绑定'];
        $rows =[];
        foreach ($unbinder as $row){
            $equid=$row['ID'];
            $row['Bind']="<button class='btn btn-success bind' data-clientid='$clientid' data-equid='$equid'>绑定 <i class='fa fa-spin fa-spinner hidden'></i></button>";
            array_push($rows,$row);
        }
        $table = new Table($headers, $rows);
        $table->class("table table-bordered table-responsive");

        //绑定处理
        Admin::script(
            <<<EOT
          $(".bind").on("click",function(){
                   var equid=$(this).attr('data-equid'); 
                   var clientid=$(this).attr('data-clientid');
                   var room=$("[name='NumBer']").val();
                    if($('input[name="NumBer"]').val()==""){
                      alert('请输入厅号');
                    }
                    else{
                      $(this).find("i").removeClass("hidden");
                      $.ajax({
                        url: '/admin/equipments/'+equid+'/bind',
                        type: 'POST',
                        data:{"ClientID":clientid,NumBer:room},
                        success: function(data) {
                               if(data.ClientID){
                                alert("绑定成功！");
                                window.location.reload();
                               }
                        },
                      });                
                    }
                  
                });
EOT

        );
        return $table->render();
    }

    public function bind(Request $request,$ID)
    {

        $equipment=Equipment::find($ID);
        $equipment->ClientID=$request->input("ClientID");
        $equipment->NumBer=$request->input("NumBer");
        $equipment->save();

        return response()->json($equipment, 200);
    }

}
