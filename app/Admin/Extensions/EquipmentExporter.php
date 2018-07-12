<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;
class EquipmentExporter extends AbstractExporter
{
    public function export()
    {
        $filename="光源信息".date("Ymdhis",time());

        Excel::create($filename, function($excel) {
            $excel->sheet('Sheetname', function($sheet) {
                $rows = collect($this->getData())->map(function ($item) {
                    //排序
                    $data_sort['ClientNum']=$item['has_one_client']['ClientNum'];
                    $data_sort['ClientName']=$item['has_one_client']['ClientName'];
                    $data_sort['NumBer']=$item['NumBer'];
                    $data_sort['TypeName']=$item['has_one_equ_type']['Name'];
                    $data_sort['EquNum']=$item['EquNum'];
                    $data_sort['RemainTime']=$item['RemainTime'];
                    $data_only=array_only($data_sort,['ClientNum','ClientName','NumBer','TypeName','EquNum','RemainTime']);
                    return $data_only;
                });
                $sheet->row(1, array(
                    '客户编号','客户名称','厅号','光源类型','光源编号','剩余时间',
                ));
                $sheet->rows($rows);
            });

        })->export('xls');
    }
}