<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Client;
use App\Admin\Models\YearDurtRept;
use App\Admin\Extensions\YearDurtExporter;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class YearDurtReptController extends Controller
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

            $content->header('时长统计');
            $content->description('光源放映时长统计');

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

            $content->header('时长统计');
            $content->description('光源放映时长统计');

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
        return Admin::grid(YearDurtRept::class, function (Grid $grid) {

            $grid->disableCreateButton()->disableActions();
            $grid->tools->disableBatchActions();
            $grid->model()->orderby('ClientName');
            $grid->ClientName('客户名称');
            $grid->NumBer('客户名称');
            $grid->Years('年份');
            $grid->一月('一月')->display(function($v){ return $v==0?0:$v;});
            $grid->二月('二月')->display(function($v){ return $v==0?0:$v;});
            $grid->三月('三月')->display(function($v){ return $v==0?0:$v;});
            $grid->四月('四月')->display(function($v){ return $v==0?0:$v;});
            $grid->五月('五月')->display(function($v){ return $v==0?0:$v;});
            $grid->六月('六月')->display(function($v){ return $v==0?0:$v;});
            $grid->七月('七月')->display(function($v){ return $v==0?0:$v;});
            $grid->八月('八月')->display(function($v){ return $v==0?0:$v;});
            $grid->九月('九月')->display(function($v){ return $v==0?0:$v;});
            $grid->十月('十月')->display(function($v){ return $v==0?0:$v;});
            $grid->十一月('十一月')->display(function($v){ return $v==0?0:$v;});
            $grid->十二月('十二月')->display(function($v){ return $v==0?0:$v;});
            $grid->Html('合计(小时)')->display(function(){
                return  $this->一月+ $this->二月+$this->三月+$this->四月+$this->五月+$this->七月+$this->八月+$this->九月+$this->十月+$this->十一月+$this->十二月+$this->十二月;
            });
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->equal('Years', '年份')->select(["2017"=>"2017","2018"=>"2018","2019"=>"2019","2020"=>"2020"]);
                $filter->equal('EquNum', '光源编号');
                $filter->equal('ClientName', '客户名称')->select(Client::all()->pluck('ClientName',"ClientName"));
            });
            $grid->exporter(new YearDurtExporter());
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(YearDurtRept::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
