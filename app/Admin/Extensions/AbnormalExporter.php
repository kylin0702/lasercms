<?php

namespace App\Admin\Extensions;

use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;
class AbnormalExporter extends AbstractExporter
{
    public function export()
    {
        $filename="充值信息".date("Ymdhis",time());

        Excel::create($filename, function($excel) {
            $excel->sheet('Sheetname', function($sheet) {
                $rows = collect($this->getData())->map(function ($item) {
                    //排序
                    $data_sort['ClientName']=$item['has_one_client']['ClientName'];
                    $data_sort['Owner']=$item['has_one_client']['Owner'];
                    $data_sort['Phone']=$item['has_one_client']['Phone'];
                    $data_sort['NumBer']=$item['has_one_equipment']['NumBer'];
                    $data_sort['ProDesc']=$item['ProDesc'];
                    $data_sort['MainteDesc']=$item['MainteDesc'];
                    $data_sort['Serious']=$item['Serious'];
                    $data_sort['Solve']=$item['Solve'];
                    $data_sort['UpdateTime']=$item['UpdateTime'];
                    $data_sort['Remark']=$item['Remark'];
                    $data_only=array_only($data_sort,['ClientName','Owner','Phone','NumBer','ProDesc','MainteDesc','Serious','Solve','UpdateTime','Remark']);
                    return $data_only;
                });
                $sheet->row(1, array(
                '客户名称','联系人','联系方式','厅号',"故障描述","处理方式","严重程度","是否解决","登记时间","备注",
                ));
                $sheet->rows($rows);
            });

        })->export('xls');
    }
}