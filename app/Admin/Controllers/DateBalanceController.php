<?php

namespace App\Admin\Controllers;

use App\Admin\Models\DateBalance;
use App\Admin\Models\Equipment;
use App\Admin\Models\EquStatus;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class DateBalanceController extends Controller
{
    use ModelForm;
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->grid());
        });
    }
    protected function grid()
    {
        return Admin::grid(DateBalance::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
        });
    }
    public  function  autocreate(){
        $redis=new \Redis();
        $balance_date=date("Y-m-d",strtotime("-1 day"));
        $equipments=Equipment::all(["ID","EquNum"]);
        $redis->set("equipments",$equipments);
        dd($redis->get("$equipments"));
        $datas= [];
        $balance_data= EquStatus::whereRaw("Updates between '2018-10-11 00:00:00.000' and '2018-10-14 23:59:59.000' ")->select(["ID","sNU"])->get();
        $redis->set("balance_data",$balance_data);
        dd($redis->get("balance_data"));
        foreach ($equipments as $equipment) {
            $first = $redis->get("balance_data")->where("sNu","=","'$equipment->EquNum'")->max("ID");
            $last =  $redis->get("balance_data")->where("sNu","=","'$equipment->EquNum'")->min("ID");
            if ($first) {
                $firsttime= EquStatus::find($first)->sTM;
                $lasttime= EquStatus::find($last)->sTM;
                $costtime=$firsttime-$lasttime;
                $data=array("EquID"=>$equipment->ID,"FirstTime" =>$firsttime,"LastTime" =>$lasttime,"CostTime"=>$costtime,"RechargeTime"=>0,"BalanceDate"=>$balance_date);
                array_push($datas,$data);
            }
        }


    }
}
