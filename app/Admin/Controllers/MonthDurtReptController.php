<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Equipment;
use Maatwebsite\Excel\Facades\Excel;
use App\Admin\Models\Client;
use App\admin\Models\DateBalance;
use App\admin\Models\V_DateBalance;
use App\Admin\Models\YearDurtRept;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;

class MonthDurtReptController extends Controller
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

            $content->header('月度统计报表');
            $content->description('');
            $output=[];
            $client=Client::all(["ID","ClientName"]);
            $month=\request("month");
            $clientid=\request("client");
            $isbuy_requst=\request("isbuy");
            if($isbuy_requst==0){$isbuy="否";}else{$isbuy="测试";}
            if(!empty($month)){
                $output=$this->month_data($month,$clientid,$isbuy);
            }
            $content->body(view("month",["output"=>$output,"client"=>$client]));
        });
    }
    //生成月度报表显示
    public  function  month_data($month,$clientid,$isbuy){
        $year=date('Y');
        $month_array=["1"=>"一月","2"=>"二月","3"=>"三月","4"=>"四月","5"=>"五月","6"=>"六月","7"=>"七月","8"=>"八月","9"=>"九月","10"=>"十月","11"=>"十一月","12"=>"十二月"];
        $month_name=$month_array[$month];
        if($month==1){
            $timespan="'$year-$month-01' and '$year-$month-25'";
        }
        else if($month==12){

            $timespan="'$year-$month-26' and '$year-$month-31'";
        }
        else{
            $lastmonth=$month-1;
            $timespan="'$year-$lastmonth-26' and '$year-$month-25'";
        }
        if(empty($clientid)){
            $items=Equipment::where("ISBuy","=",$isbuy)->orderBy("ClientID")->get(["ID"]);
        }
        else{
            $items=Equipment::where("ISBuy","=",$isbuy)->where("ClientID","=",$clientid)->get(["ID"]);
        }
        $output=[];
        $all_data_month=V_DateBalance::whereRaw("BalanceDate Between  $timespan")->get();
        $all_years= YearDurtRept::whereRaw("Years='$year'")->get();
        foreach ($items as $item){
            $equipment=$all_data_month->where('EquID', "$item->ID");
            if(!empty($equipment->first())) {
                $assetno = $equipment->first()->AssetNo;
                $clientname= $equipment->first()->ClientName;
                $clientsn= $equipment->first()->ClientSN;
                $number= $equipment->first()->NumBer;
                $lastmonth_remain = $equipment->first()->FirstTime;//上月剩余时间
                $typename = $equipment->first()->TypeName;
                $equnum = $equipment->first()->EquNum;
                $sum_recharge = $equipment->sum('RechargeTime');//本月总充值时间
                $sum_costtime = 0;//本月使用时间
                $month_remain = $equipment->last()->LastTime;//本月剩余时间
                $y = $all_years->where("EquID","$item->ID")->first();
                if (!empty($y)) {
                    $yeartotal = $y->一月 + $y->二月 + $y->三月 + $y->四月 + $y->五月 + $y->六月 + $y->七月 + $y->八月 + $y->九月 + $y->十月 + $y->十一月 + $y->十二月;
                    $y_toarray=$y->toArray();
                    $sum_costtime=$y_toarray["$month_name"];
                } else {
                    $yeartotal = 0;
                }
                $month_ded = 0;
                if ($sum_costtime < 200) {
                    $month_ded = 200 - $sum_costtime;//使用时间不超过200小时，应扣小时数为200-使用小时数
                }

                $row = ["客户编码"=>$clientsn,
                    "财产编号" => $assetno,
                    "客户名称"=>$clientname,
                    "厅号" => $number,
                    "机型" => $typename,
                    "光源编号" => $equnum,
                    "上月余额小时" => $lastmonth_remain,
                    "本月充值小时数(含赠送)" => $sum_recharge,
                    "本月使用小时数" => $sum_costtime,
                    "剩余小时数" => $month_remain,
                    "本年累计小时数" => $yeartotal,
                    "本月应扣除小时数" => $month_ded];
                array_push($output, $row);
            }
        }
        return $output;
    }

    //生成月度Excel报表
    public  function  month_excel(Request $request){
        $year=date('Y');
        $month=$request->get("month");
        $clientid=\request("client");
        $isbuy_requst=\request("isbuy");
        if($isbuy_requst==0){$isbuy="否";}else{$isbuy="测试";}
        $month_array=["1"=>"一月","2"=>"二月","3"=>"三月","4"=>"四月","5"=>"五月","6"=>"六月","7"=>"七月","8"=>"八月","9"=>"九月","10"=>"十月","11"=>"十一月","12"=>"十二月"];
        $month_name=$month_array[$month];
        if($month==1){
            $timespan="'$year-$month-01' and '$year-$month-25'";
        }
        else if($month==12){

            $timespan="'$year-$month-26' and '$year-$month-31'";
        }
        else{
            $lastmonth=$month-1;
            $timespan="'$year-$lastmonth-26' and '$year-$month-25'";
        }
        if(empty($clientid)){
            $items=Equipment::where("ISBuy","=",$isbuy)->orderBy("ClientID")->get(["ID"]);
        }
        else{
            $items=Equipment::where("ISBuy","=",$isbuy)->where("ClientID","=",$clientid)->get(["ID"]);
        }
        $export_excel_data=[];
        $all_data_month=V_DateBalance::whereRaw("BalanceDate Between  $timespan")->get();
        $all_years= YearDurtRept::whereRaw("Years='$year'")->get();
        foreach ($items as $item){
            $equipment=$all_data_month->where('EquID', "$item->ID");
            if(!empty($equipment->first())) {
                $assetno = $equipment->first()->AssetNo;
                $clientname= $equipment->first()->ClientName;
                $clientsn= $equipment->first()->ClientSN;
                $number= $equipment->first()->NumBer;
                $lastmonth_remain = $equipment->first()->FirstTime;//上月剩余时间
                $typename = $equipment->first()->TypeName;
                $equnum = $equipment->first()->EquNum;
                $sum_recharge = $equipment->sum('RechargeTime');//本月总充值时间
                $sum_costtime = 0;//本月使用时间
                $month_remain = $equipment->last()->LastTime;//本月剩余时间
                $y = $all_years->where("EquID","$item->ID")->first();
                if (!empty($y)) {
                    $yeartotal = $y->一月 + $y->二月 + $y->三月 + $y->四月 + $y->五月 + $y->六月 + $y->七月 + $y->八月 + $y->九月 + $y->十月 + $y->十一月 + $y->十二月;
                    $y_toarray=$y->toArray();
                    $sum_costtime=$y_toarray["$month_name"];
                } else {
                    $yeartotal = 0;
                }
                $month_ded = 0;
                if ($sum_costtime < 200) {
                    $month_ded = 200 - $sum_costtime;//使用时间不超过200小时，应扣小时数为200-使用小时数
                }

                $row = ["客户编码"=>$clientsn,
                    "财产编号" => $assetno,
                    "客户名称"=>$clientname,
                    "厅号" => $number,
                    "机型" => $typename,
                    "光源编号" => $equnum,
                    "上月余额小时" => $lastmonth_remain,
                    "本月充值小时数(含赠送)" => $sum_recharge,
                    "本月使用小时数" => $sum_costtime,
                    "剩余小时数" => $month_remain,
                    "本年累计小时数" => $yeartotal,
                    "本月应扣除小时数" => $month_ded];
                array_push($export_excel_data, $row);
            }

        }
        $filename=$year."年".$month."月月度使用时长报表";
        return Excel::create($filename, function($excel) use($export_excel_data) {
            $excel->sheet('Sheetname', function($sheet) use($export_excel_data) {
                $rows = collect($export_excel_data)->map(function ($item) {
                    $data=array_only($item,[ '客户编码','财产编号','客户名称','厅号','机型','光源编号','上月余额小时','本月充值小时数(含赠送)','本月使用小时数','剩余小时数','本年累计小时数','本月应扣除小时数']);
                    return $data;
                });
                $sheet->row(1, array(
                    '客户编码','财产编号','客户名称','厅号','机型','光源编号','上月余额小时','本月充值小时数(含赠送)','本月使用小时数','剩余小时数','本年累计小时数','本月应扣除小时数'
                ));
                $sheet->rows($rows);
            });

        })->export('xls');

    }



}
