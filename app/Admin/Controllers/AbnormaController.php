<?php

namespace App\Admin\Controllers;

use App\admin\Models\Abnorma;

use App\Admin\Models\Client;
use App\Admin\Models\Equipment;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use function foo\func;

class AbnormaController extends Controller
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

            $content->header('异常记录');
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

            $content->header('修改异常记录');
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

            $content->header('增加异常记录');
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
        return Admin::grid(Abnorma::class, function (Grid $grid) {
            $grid->tools->disableBatchActions();
            $grid->disableRowSelector();
            $grid->hasOneClient()->ClientName("客户名称");
            $grid->hasOneClient()->Owner("联系人");
            $grid->hasOneClient()->Phone("联系方式");
            $grid->hasOneEquipment()->NumBer("厅号");
            $grid->ProDesc("故障现象");
            $grid->Livephotos("现场图片1")->display(function ($v){
                $html=<<<EOT
                <button type="button" class="btn btn-warning"  data-html="true" data-toggle="popover" title="现场图片2" data-content="<image src='/uploads/$v' />">查看 <i class="fa fa-image"></i></button>
EOT;
                return $html;
            });
            $grid->Livephotos2("现场图片1")->display(function ($v){
                $html=<<<EOT
                <button type="button" class="btn btn-warning"  data-html="true" data-toggle="popover" title="现场图片2" data-content="<image src='/uploads/$v' />">查看 <i class="fa fa-image"></i></button>
EOT;
                return $html;
            });
            $grid->MainteDesc("处理过程");
            $grid->Remark("备注");
            $grid->Serious("严重程度")->display(function($v){
                $serios=["一般"=>"primary","严重"=>"warning","特别严重"=>"danger"];
                return "<Label class='label label-$serios[$v]'>$v</Label>";
            });
            $grid->Solve("是否解决");
            $grid->UpdateTime("登记时间");
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Abnorma::class, function (Form $form) {
            $form->select("ClientID","客户名称")->options(Client::all()->pluck('ClientName',"ID"))->setWidth(2)->load("EquID","/admin/equipments/getRoom","ID","NumBer");
            $form->select('EquID', '厅号')->setWidth(2)->options(function($v){
                if(!empty($v)) {
                    $clientid = Equipment::find($v)->ClientID;
                    $data = [];
                    $rooms = Equipment::where("ClientID", "=", $clientid)->get();
                    foreach ($rooms as $item) {
                        $data[$item["ID"]] = $item["NumBer"];
                    }
                    return $data;
                }
            });
            $form->textarea("ProDesc","故障现象")->setWidth(4);
            $form->file("Livephotos","现场图片1")->setWidth(4);
            $form->file("Livephotos2","现场图片2")->setWidth(4);
            $form->select("Serious","严重程度")->options(["一般"=>"一般","严重"=>"严重","特别严重"=>"特别严重"])->setWidth(2);
            $form->text("Remark","备注");
            $form->hidden("UpdateTime")->default(function (){
                return date("Y-m-d H:i:s");
            });
            $form->hidden("Solve")->default("未解决");



        });
    }
}
