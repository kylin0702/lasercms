<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Client;
use App\admin\Models\DateBalance;
use App\admin\Models\V_DateBalance;
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

    public function query($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('任意时长查询');
            $content->description('如果光源在使用期间没有联网,联网后累加在当天的使用时长上');
            $balances=[];
            $total=0;
            if(request("date1")){
            $balances=V_DateBalance::where(function($q) use ($id) {
                $q-> where("EquId","=",$id)
                    ->where("BalanceDate",">=",request("date1"))
                    ->where("BalanceDate","<=",request("date2"));
            })->get();
            $total=$balances->sum("CostTime");
            }
            $content->body(view("durt",["id"=>$id,"balances"=>$balances,"total"=>$total,"date1"=>request("date1"),"date2"=>request("date2")]));

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

            $grid->disableCreateButton();
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
            $grid->actions(function($actions){
                $actions->disableDelete();
                $actions->disableEdit();
                $id=$actions->row->EquID;
                $actions->append("<a href='/admin/durt/query/$id' class='btn btn-info btn-xs' >明细查询</i></a>");
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
