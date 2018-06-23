<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Client;
use App\Admin\Models\YearDurtRept;

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
            $grid->model()->orderby('Years','desc');
            $grid->ClientName('客户名称');
            $grid->EquNum('光源序列');
            $grid->Years('年份');
            $grid->January('一月')->display(function($v){ return $v==0?0:$v;});
            $grid->February('二月')->display(function($v){ return $v==0?0:$v;});
            $grid->March('三月')->display(function($v){ return $v==0?0:$v;});
            $grid->April('四月')->display(function($v){ return $v==0?0:$v;});
            $grid->May('五月')->display(function($v){ return $v==0?0:$v;});
            $grid->June('六月')->display(function($v){ return $v==0?0:$v;});
            $grid->July('七月')->display(function($v){ return $v==0?0:$v;});
            $grid->August('八月')->display(function($v){ return $v==0?0:$v;});
            $grid->September('九月')->display(function($v){ return $v==0?0:$v;});
            $grid->October('十月')->display(function($v){ return $v==0?0:$v;});
            $grid->November('十一月')->display(function($v){ return $v==0?0:$v;});
            $grid->December('十二月')->display(function($v){ return $v==0?0:$v;});
            $grid->Html('合计(小时)')->display(function(){
                return  $this->January+ $this->February+$this->March+$this->April+$this->May+$this->June+$this->July+$this->August+$this->September+$this->October+$this->November+$this->December;
            });
            $grid->filter(function ($filter) {
                $filter->disableIdFilter();
                $filter->equal('Years', '年份')->select(["2017"=>"2017","2018"=>"2018","2019"=>"2019","2020"=>"2020"]);
                $filter->equal('EquNum', '光源编号');
                $filter->equal('ClientName', '客户名称')->select(Client::all()->pluck('ClientName',"ClientName"));
            });
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
