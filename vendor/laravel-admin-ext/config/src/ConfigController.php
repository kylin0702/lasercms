<?php

namespace Encore\Admin\Config;

use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;

class ConfigController
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
            $content->header('手机设置');
            $content->description('');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $content->header('手机设置');
            $content->description('验证码接收手机设置');
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

    public function grid()
    {
        return Admin::grid(ConfigModel::class, function (Grid $grid) {
            $grid->tools->disableBatchActions();
            $grid->tools->disableRefreshButton();
            $grid->tools->append("<i class='fa fa-mobile'></i> 验证码接收手机设置");
            $grid->disableRowSelector()->disableFilter()->disableExport()->disableCreateButton()->disablePagination();
            $grid->actions(function($actions){
                $actions->disableDelete();
                $actions->disableEdit();
                $id=$actions->getKey();
                $actions->append("<a href='/admin/config/$id/edit'><i class='fa fa-edit'></i></a>");
            });
            $grid->name("标识")->display(function ($name) {
                return "<a tabindex=\"0\" class=\"btn btn-xs btn-twitter\" role=\"button\" data-toggle=\"popover\" data-html=true title=\"Usage\" data-content=\"<code>config('$name');</code>\">$name</a>";
            });
            $grid->value("手机号码");
            $grid->description("描述");

           // $grid->created_at();
            //$grid->updated_at();

            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->like('name');
                $filter->like('value');
            });
        });
    }

    public function form()
    {
        return Admin::form(ConfigModel::class, function (Form $form) {
            //$form->display('id', 'ID');
            $form->display('name',"标识(不能修改)");
            $form->mobile('value',"手机号码")->rules('required');
            $form->textarea('description',"描述");
            //$form->display('created_at');
            //$form->display('updated_at');
        });
    }
}
