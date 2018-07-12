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
                    $data=array_dot($item);
                    dd($item);
                    $data_only=array_only($data,['has_one_client.ClientNum','has_one_client.ClientName','NumBer','has_one_equ_type.Name','EquNum','RemainTime']);
                    return array_reverse($data_only);
                });
                $sheet->row(1, array(
                    '光源类型', '客户名称','客户编号','光源编号','剩余时间','厅号'
                ));
                $sheet->rows($rows);
            });

        })->export('xls');
    }
}