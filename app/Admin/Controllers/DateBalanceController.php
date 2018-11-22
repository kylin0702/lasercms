<?php

namespace App\Admin\Controllers;

use App\Admin\Models\DateBalance;
use App\Admin\Models\Equipment;
use App\Admin\Models\EquStatus;
use App\Admin\Models\EquStatusTemp;
use App\admin\Models\Recharge;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;
use Symfony\Component\VarDumper\Cloner\Data;

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

        });
    }
     //天结算
    public  function  autocreate(){
        $balance_date=date("Y-m-d",strtotime("-1 day"));
        $balance_timespan="'$balance_date 00:00:00.000' and '$balance_date 23:59:59.000'";
        $count=DateBalance::where("BalanceDate","=","$balance_date")->count();
        if($count==0){
            $datas= [];
            //取每天Equstatus最大的ID值 或最小的ID值， 用于取得光源每天第一次上传的剩余时间和最后一次的剩余时间
            $query="select A.EquID,MinID,MaxID from (select min(ID) as MinID, EquID from V_Equipment_EquStatus where Updates between $balance_timespan  group by EquID) A left join 
                (select max(ID) as MaxID, EquID from V_Equipment_EquStatus where Updates between $balance_timespan group by EquID) B on A.EquID=B.EquID";
            $balance_data= DB::select( $query);
            foreach ($balance_data as $v){
                $count_min=EquStatus::where("ID","=",$v->MinID)->count();
                $count_max=EquStatus::where("ID","=",$v->MaxID)->count();
                if($count_min>0&&$count_max>0){
                    $firsttime = EquStatus::find($v->MinID)->sTM;
                    $lasttime = EquStatus::find($v->MaxID)->sTM;
                    $rechargetime = Recharge::whereRaw("EquID=$v->EquID and Results='1' and UpdateTime between $balance_timespan")->select(['RechTime'])->get('RechTime')->sum('RechTime');
                    $costtime = $firsttime+$rechargetime-$lasttime;//每天第一次上传时间+当天充值时间-最后一次剩余时间得出使用时间
                    //不正常数据设置0
                    if($costtime<0||$costtime>200){
                        $costtime=0;
                    }
                    $item = array("EquID" => $v->EquID, "FirstTime" => $firsttime, "LastTime" => $lasttime,"RechargeTime" => $rechargetime,"CostTime" => $costtime, "BalanceDate" => "$balance_date");
                    array_push($datas,$item);
                }
            }
            DB::beginTransaction();
            DB::table("DateBalance")->insert($datas);
            try {
                DB::commit();
                return json_encode(["result"=>"true","message"=>"Date Balance Create Finished"]);
            }
            catch (\Exception $e){
                return json_encode(["result"=>"fasle","message"=>$e->getMessage()]);
            }
        }
        else{
            return json_encode(["result"=>"fasle","message"=>"Date Balance Have Done,Not Thing To Do"]);
        }
    }
    //时间段天结算
    public  function  autocreate_all(){
        $start=new \DateTime('2018-03-12');
        $end=new \DateTime('2018-09-13');
        $dates=array();
        foreach(new \DatePeriod($start,new \DateInterval('P1D'),$end) as $d){
            array_push($dates,$d->format('Y-m-d'));
        }
        foreach ($dates as $d){
            $balance_date=$d;
            $balance_timespan="'$balance_date 00:00:00.000' and '$balance_date 23:59:59.000'";
            $count=DateBalance::where("BalanceDate","=","$balance_date")->count();
            if($count==0){
                $datas= [];
                //取每天Equstatus最大的ID值 或最小的ID值， 用于取得光源每天第一次上传的剩余时间和最后一次的剩余时间
                $query="select A.EquID,MinID,MaxID from (select min(ID) as MinID, EquID from V_Equipment_EquStatusTemp where Updates between $balance_timespan  group by EquID) A left join 
                (select max(ID) as MaxID, EquID from V_Equipment_EquStatusTemp where Updates between $balance_timespan group by EquID) B on A.EquID=B.EquID";
                $balance_data= DB::select( $query);
                foreach ($balance_data as $v){
                    $count_min=EquStatusTemp::where("ID","=",$v->MinID)->count();
                    $count_max=EquStatusTemp::where("ID","=",$v->MaxID)->count();
                    if($count_min>0&&$count_max>0){
                        $firsttime = EquStatusTemp::find($v->MinID)->sTM;
                        $lasttime = EquStatusTemp::find($v->MaxID)->sTM;
                        $rechargetime = Recharge::whereRaw("EquID=$v->EquID and Results='1' and UpdateTime between $balance_timespan")->select(['RechTime'])->get('RechTime')->sum('RechTime');
                        $costtime = $firsttime+$rechargetime- $lasttime;//每天第一次上传时间+当天充值时间-最后一次剩余时间得出使用时间
                        //不正常数据设置0
                        if($costtime<0||$costtime>100){
                            $costtime=0;
                        }
                        $item = array("EquID" => $v->EquID, "FirstTime" => $firsttime, "LastTime" => $lasttime,"RechargeTime" => $rechargetime,"CostTime" => $costtime, "BalanceDate" => "$balance_date");
                        array_push($datas,$item);
                    }
                }
                DB::table("DateBalance")->insert($datas);
            }
        }
        return json_encode(["result"=>"true","message"=>"Date Balance Create Finished"]);
    }
}
