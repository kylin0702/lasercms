<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Area;
use App\admin\Models\Client;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

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
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Client::class, function (Grid $grid) {

            $grid->ClientNum('客户编号');
            $grid->ClientName('影城名称')->display(function ($v){
                $id=$this->getKey();
                return  "<a href='equipments/$id/clientshow'>$v</a>";
            });
            $grid->Adress('影城地址');
            #$grid->JoinHotline('加盟热线');
            $grid->VideoNum('影厅数量');
            $grid->Owner('影城法人');
            $grid->Phone('联系方式');
            $grid->UpdateTime('合作时间')->display(function ($v){
                return date("Y-m-d",strtotime($v));
            });
            $grid->area()->AreaCode("区域代码");
            $grid->area()->AreaName("区域名称");

            $grid->Review('审核状态')->display(function ($v){
                if($v=="已审核"){
                    return "<label class='label label-success'>$v</label>";
                }
                else{
                    return "<label class='label label-warning'>$v</label>";
                }
            });
            $grid->auditor()->name("审核人");
            $grid->Remark('备注');
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
            $form->ignore(["Superior"]);
            $form->text('ClientNum', '客户编号')->setWidth(5);
            $form->text('ClientName', '影城名称')->setWidth(5);
            $form->text('Adress', '影城地址');
            #$form->mobile('JoinHotline', '加盟热线');
            $form->number('VideoNum', '影厅数量');
            $form->text('Owner', '影城法人')->setWidth(2);
            $form->mobile('Phone', '联系方式');
            $form->date('UpdateTime', '合作时间');
            $form->select('area.Superior', '父级区域')->options(function (){
                $data=[];
                $superior=Area::where("Superior","=",0)->get();
                foreach ($superior as $item){
                    $data[$item["ID"]]=$item["AreaName"];
                }
                return $data;
            })->load("AreaID","/admin/areas/getSonArea","ID","AreaName");
            $form->select('AreaID', '区域名称');
            $states = [
                'on'  => ['value' => '已审核', 'text' => '已审核', 'color' => 'success'],
                'off' => ['value' => '未审核', 'text' => '未审核', 'color' => 'danger'],
            ];
            $form->switch('Review','审核状态')->states($states);
            $form->text('EntryPer', '审核人');
        });
    }
}
