<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Equipment;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\InfoBox;

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
              $grid->Client()->ClientNum('客户编号');
               $grid->Client()->ClientName('客户名称');
               $grid->NumBer('影厅号');
               $grid->EquType()->Name('光源类型');
                #$grid->EquType()->Price('单价');
               #$grid->EquType()->GiftTime('赠送时长');
               $grid->RemainTime('剩余时长');
               #$grid-> EquNum('光源编号');
               $grid-> EquNum('光源编号')->display(function($v){
                   #to be continue..
                   return "<a href='equstatus/$v/show'>$v</a>";
               });
               $grid->EquStatus('光源状态');
               $grid->Review('审核状态');
               $grid->ISBuy('是否购买');
               $grid->EntryPer('录入人');
               $grid->Auditor('是否审核');
               $grid->ReviewTime('审核时间');
               $grid->Precharge('是否预充值');
               $grid->PreGift('是否预赠送时长');
               $grid->IsPre('是否已充充值成功');
               $grid->IsSend('是否发送短信');
               $grid->IsEnabled('是否记用');
               $grid->IsDelay('延迟充值');
               $grid->IsDelay('延迟充值');
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
