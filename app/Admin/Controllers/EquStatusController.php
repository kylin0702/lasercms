<?php

namespace App\Admin\Controllers;

use App\admin\Models\EquStatus;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class EquStatusController extends Controller
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

            $content->header('header');
            $content->description('description');

            $content->body($this->grid());
        });
    }

    /**
     * Show interface.
     *
     * @param $id
     * @return Content
     */
    public function show($sn)
    {
        return Admin::content(function (Content $content) use ($sn) {
        $content->header('header');
        $content->description('description');
        $content->body($this->form()->view($sn));
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
        return Admin::grid(EquStatus::class, function (Grid $grid) {
            $grid->model()->where('id', '<', 100);
            $grid->id('ID')->sortable();
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(EquStatus::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->display('sNU', 'sNU');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function view()
    {
        return Admin::form(EquStatus::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->display('sNU', 'sNU');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
