<?php

namespace App\Admin\Controllers;

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
            $grid->ClientName('影城名称');
            $grid->Adress('影城地址');
            #$grid->JoinHotline('加盟热线');
            $grid->VideoNum('影厅数量');
            $grid->Owner('影城法人');
            $grid->Phone('联系方式');
            $grid->UpdateTime('合作时间');
            $grid->AreaID('所属区域ID ');
            $grid->Review('审核状态');
            $grid->EntryPer('审核人');
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

            $form->text('ClientNum', '客户编号')->setWidth(5);
            $form->text('ClientName', '影城名称')->setWidth(5);
            $form->text('Adress', '影城地址');
            #$form->mobile('JoinHotline', '加盟热线');
            $form->number('VideoNum', '影厅数量');
            $form->text('Owner', '影城法人')->setWidth(2);
            $form->mobile('Phone', '联系方式');
            $form->date('UpdateTime', '合作时间');
            $form->divide();
            $form->text('AreaID', '所属区域ID');
            $states = [
                'on'  => ['value' => '已审核', 'text' => '已审核', 'color' => 'success'],
                'off' => ['value' => '未审核', 'text' => '未审核', 'color' => 'danger'],
            ];
            $form->switch('Review','审核状态')->states($states);
            $form->text('EntryPer', '审核人');
            $form->text('Remark', '备注');
        });
    }
}
