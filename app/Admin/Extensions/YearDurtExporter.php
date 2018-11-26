<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;
class YearDurtExporter extends AbstractExporter
{
    public function export()
    {
        $filename="时长统计".date("Ymdhis",time());

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

        })->export('xls');
    }
}