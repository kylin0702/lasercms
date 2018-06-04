<?php

namespace App\Admin\Controllers;

use App\Admin\Models\YearCostRept;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class YearCostReptController extends Controller
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

            $content->header('金额统计');
            $content->description('光源消费金额统计');

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
        return Admin::grid(YearCostRept::class, function (Grid $grid) {

            $grid->disableCreateButton()->disableActions();
            $grid->tools->disableBatchActions();
            $grid->Years('年份');
            $grid->January('一月');
            $grid->February('二月');
            $grid->March('三月');
            $grid->April('四月');
            $grid->May('五月');
            $grid->June('六月');
            $grid->July('七月');
            $grid->August('八月');
            $grid->September('九月');
            $grid->October('十月');
            $grid->November('十一月');
            $grid->December('十二月');

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(YearCostRept::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
