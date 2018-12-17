<?php

namespace App\Admin\Controllers;

use App\Admin\Models\DateBalance;
use App\Admin\Models\Equipment;
use App\Admin\Models\EquStatus;
use App\Admin\Models\EquStatusTemp;
use App\admin\Models\Recharge;
use App\Admin\Models\YearDurtRept;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel;
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
    //生成月度Excel报表
    public  function  month_excel(Request $request){
        $year=2018;
        $month=10;
        $timespan="";
        if($month==1){
            $timespan="BalanceDate Between '$year-$month-01' and '$year-$month-25'";
        }
        else if($month==12){
            $month=$month-1;
            $timespan="BalanceDate Between '$year-$month-25' and '$year-$month-31'";
        }
        else{
            $timespan="BalanceDate Between '$year-$month-25' and '$year-$month-26'";
        }
        $month_datas=DateBalance::whereRaw($timespan)->get();
        $export_excel_data=[];
        foreach ($month_datas as $v){
            $equipment=DateBalance::where("EquID","=",$v->EquID);
            $lastmonth_remain=$equipment->first()->FirstTime;//上月剩余时间
            $sum_recharge=$equipment->sum('RechargeTime');//本月总充值时间
            $sum_costtime=$equipment->sum('CostTime');//本月使用时间
            $month_remain=$equipment->orderBy("ID","desc")->first()->LastTime;//本月剩余时间
            $month_ded=0;
            if($sum_costtime<200){
                $month_ded=200-$sum_costtime;//使用时间不超过200小时，应扣小时数为200-使用小时数
            }
            if($sum_costtime)

            $y=YearDurtRept::whereRaw("EquID='$v->EquID' and Years='$year'")->first();
            $yeartotal=$y->一月+$y->二月+$y->三月+$y->四月+$y->五月+$y->六月+$y->七月+$y->八月+$y->九月+$y->十月+$y->十一月+$y->十二月;
            $row=["机型"=>$v->TypeName,"光源编号"=>$v->EquNum,"上月余额小时"=>$lastmonth_remain,"本月充值小时数(含赠送)"=>$sum_recharge,	"本月使用小时数	"=>$sum_costtime,"剩余小时数"=>$month_remain,"本年累计小时数"=>$yeartotal,"本月应扣除小时数"=>$month_ded];
        }
        /*$filename=$year."年".$month."月月度使用时长报表";
        Excel::create($filename, function($excel) {
            $excel->sheet('Sheetname', function($sheet) {
                $rows = collect($this->getData())->map(function ($item) {
                    $item['年度合计']=$item['一月']+$item['二月']+$item['三月']+$item['四月']+$item['五月']+$item['六月']+$item['七月']+$item['八月']+$item['九月']+$item['十月']+$item['十一月']+$item['十二月'];
                    $data=array_only($item,['ClientName','NumBer','EquNum','Years','一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','年度合计']);
                    return $data;
                });
                $sheet->row(1, array(
                    '客户名称','厅号','光源编号','年份','一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月','年度合计'
                ));
                $sheet->rows($rows);
            });

        })->export('xls');*/

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
                    $count_lastbalance=DateBalance::where("EquID","=",$v->EquID)->orderBy("BalanceDate","Desc")->count();
                    if($count_lastbalance>0){
                        $lasttime_lastbalance=DateBalance::where("EquID","=",$v->EquID)->orderBy("BalanceDate","Desc")->first()->LastTime;
                    }
                    else{
                        $lasttime_lastbalance=$firsttime;
                    }
                    $lost=$lasttime_lastbalance-$firsttime;//用户没联网丢失的使用时长
                    $rechargetime = Recharge::whereRaw("EquID=$v->EquID and Results='1' and UpdateTime between $balance_timespan")->select(['RechTime'])->get('RechTime')->sum('RechTime');
                    $costtime = $firsttime+$rechargetime-$lasttime+$lost;//每天第一次上传时间+当天充值时间-最后一次剩余时间得出使用时间
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
        $start=new \DateTime('2018-11-25');
        $end=new \DateTime('2018-11-26');
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
                $query="select A.EquID,MinID,MaxID from (select min(ID) as MinID, EquID from V_Equipment_EquStatus where Updates between $balance_timespan  group by EquID) A left join 
                (select max(ID) as MaxID, EquID from V_Equipment_EquStatus where Updates between $balance_timespan group by EquID) B on A.EquID=B.EquID";
                $balance_data= DB::select( $query);
                foreach ($balance_data as $v){
                    $count_min=EquStatus::where("ID","=",$v->MinID)->count();
                    $count_max=EquStatusTemp::where("ID","=",$v->MaxID)->count();
                    if($count_min>0&&$count_max>0){
                        $firsttime = EquStatus::find($v->MinID)->sTM;
                        $lasttime = EquStatus::find($v->MaxID)->sTM;
                        $count_lastbalance=DateBalance::where("EquID","=",$v->EquID)->orderBy("BalanceDate","Desc")->count();
                        if($count_lastbalance>0){
                            $lasttime_lastbalance=DateBalance::where("EquID","=",$v->EquID)->orderBy("BalanceDate","Desc")->first()->LastTime;
                        }
                        else{
                            $lasttime_lastbalance=$firsttime;
                        }
                        //最后一次结算的LastTime
                        $lost=$lasttime_lastbalance-$firsttime;//用户没联网丢失的使用时长
                        $rechargetime = Recharge::whereRaw("EquID=$v->EquID and Results='1' and UpdateTime between $balance_timespan")->select(['RechTime'])->get('RechTime')->sum('RechTime');
                        $costtime = $firsttime+$rechargetime- $lasttime+$lost;//每天第一次上传时间+当天充值时间-最后一次剩余时间得出使用时间+用户没联网丢失时长
                        //不正常数据设置0
                        if($costtime<0||$costtime>200){
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
