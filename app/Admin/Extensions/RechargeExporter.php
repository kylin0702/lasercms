<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;
class RechargeExporter extends AbstractExporter
{
    public function export()
    {
        $filename="充值信息".date("Ymdhis",time());

        Excel::create($filename, function($excel) {
            $excel->sheet('Sheetname', function($sheet) {
                $rows = collect($this->getData())->map(function ($item) {
                    //排序
                    $data_sort['SerialNumber']=$item['SerialNumber'];
                    $data_sort['ClientName']=$item['has_one_client']['ClientName'];
                    $data_sort['NumBer']=$item['has_one_equ']['NumBer'];
                    $data_sort['EquNum']=$item['has_one_equ']['EquNum'];
                    $data_sort['Method']=$item['Method']==0?"网上充值":"系统赠送";
                    $data_sort['RechTime']=$item['RechTime'];
                    $data_sort['IP']=$item['IP'];
                    $data_sort['UpdateTime']=$item['UpdateTime'];
                    $data_sort['Results']=$item['Results']==1?"充值成功":"充值失败";
                    $data_only=array_only($data_sort,['SerialNumber','ClientNum','ClientName','NumBer','EquNum','Method','RechTime',"IP","UpdateTime","Results"]);
                    return $data_only;
                });
                $sheet->row(1, array(
                    '订单编号','客户名称','厅号','光源编号','充值方式',"充值小时数","充值IP","充值时间","充值结果",
                ));
                $sheet->rows($rows);
            });

        })->export('xls');
    }
}