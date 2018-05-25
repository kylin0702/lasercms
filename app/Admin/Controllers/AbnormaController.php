<?php

namespace App\Admin\Controllers;

use App\admin\Models\Abnorma;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

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

            $content->header('异常记录');
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

            $content->header('异常记录');
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
            $grid->Livephotos("现场图片1");
            $grid->Livephotos2("现场图片1");
            $grid->MainteDesc("处理过程");
            $grid->Remark("备注");
            $grid->Serious("严重程度");
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

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
