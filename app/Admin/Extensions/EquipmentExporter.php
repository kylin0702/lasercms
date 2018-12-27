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
                    if($item['ISBuy']=="是"){
                        $data_sort['ISBuy']='销售';
                    }
                    elseif ($item['ISBuy']=="测试"){
                        $data_sort['ISBuy']='测试';
                    }
                    else{
                        $data_sort['ISBuy']='租赁';
                    }
                    $data_sort['EquNum']=$item['EquNum'];
                    $data_sort['RemainTime']=$item['RemainTime'];
                    $data_sort['ReviewTime']=$item['ReviewTime'];
                    $data_only=array_only($data_sort,['ClientNum','ClientName','NumBer','TypeName','ISBuy','EquNum','RemainTime','ReviewTime']);
                    return $data_only;
                });
                $sheet->row(1, array(
                    '客户编号','客户名称','厅号','光源类型','销售类型','光源编号','剩余时间','最后通讯时间'
                ));
                $sheet->rows($rows);
            });

        })->export('xls');
    }
}