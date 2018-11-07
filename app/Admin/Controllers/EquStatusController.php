<?php

namespace App\Admin\Controllers;

use App\admin\Models\EquStatus;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Maatwebsite\Excel\Classes\PHPExcel;
use Maatwebsite\Excel\Facades\Excel;
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Table;
use Illuminate\Http\Request;

class EquStatusController extends Controller
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

            $content->header('header');
            $content->description('description');
        });
    }

    /**
     * Show interface.
     *
     * @param $id
     * @return Content
     */
    public function show($sn)
    {
        return Admin::content(function (Content $content) use ($sn) {
        $content->header('header');
        $content->description('description');
        $content->body($this->view()->view($sn));
        });
    }


    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(EquStatus::class, function (Grid $grid) {
            $grid->model()->where('id', '<', 100);
            $grid->id('ID')->sortable();
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(EquStatus::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->display('sNU', 'sNU');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function view()
    {
        return Admin::form(EquStatus::class, function (Form $form) {
            $form->display('sNU', 'sNU')->with(function($v){

            });
        });
    }
    //通过客户ID返回光源
    public function getStatus(Request $request){
        $snu = $request->get('s');
        return EquStatus::where('sNU',"=", $snu)->orderby('ID','desc')->first();
    }
    //通过客户ID返回光源
    public function exportExcel(Request $request){
        $filename="光源状态记录(".$request->get('snu').")".rand(100,999);//文件名:月份+月时长使用报表+当前日期+3位随机数
        /*return Excel::create($filename, function($excel) use ($request) {
            $snu = $request->get('snu');
            $date1=$request->get('date1');
            $date2=$request->get('date2');
            $excel->sheet($snu, function($sheet) use($snu,$date1,$date2){
                $status=EquStatus::whereRaw("sNu='$snu' and UpDates Between '$date1' and '$date2'")->get()->toArray();
                dd($status);
                $sheet->row(1, array(
                    'UpDates','sMT','sMS','sTM','sLI','sURT1','sURL','sURC1','sURC2','sURC3','sURC4','sURC5','sURC6','sURC7',
                    'sURC8','sURC9','sURC10','sURC11','sURC12','sURC13','sURC14','sURC15','sUGT1','sUGL','sUGC1','sUGC2','sUGC3',
                    'sUGC4','sUGC5','sUGC6','sUGC7','sUGC8','sUGC9','sUGC10','sUGC11','sUGC12','sUGC13','sUGC14','sUGC15','sUBT',
                    'sUBL','sUBC1','sUBC2','sUBC3','sUBC4','sDRT1','sDRL','sDRC1','sDRC2','sDRC3','sDRC4','sDRC5','sDRC6','sDRC7',
                    'sDRC8','sDRC9','sDRC10','sDRC11','sDRC12','sDRC13','sDRC14','sDRC15','sDGT1','sDGL','sDGC1','sDGC2','sDGC3',
                    'sDGC4','sDGC5','sDGC6','sDGC7','sDGC8','sDGC9','sDGC10','sDGC11','sDGC12','sDGC13','sDGC14','sDGC15','sDBT',
                    'sDBL','sDBC1','sDBC2','sDBC3','sDBC4'
                ));
                $rows = collect($status)->map(function ($item) {
                    $data_only=array_only($item,[
                        'UpDates','sMT','sMS','sTM','sLI','sURT1','sURL','sURC1','sURC2','sURC3','sURC4','sURC5','sURC6','sURC7',
                        'sURC8','sURC9','sURC10','sURC11','sURC12','sURC13','sURC14','sURC15','sUGT1','sUGL','sUGC1','sUGC2','sUGC3',
                        'sUGC4','sUGC5','sUGC6','sUGC7','sUGC8','sUGC9','sUGC10','sUGC11','sUGC12','sUGC13','sUGC14','sUGC15','sUBT',
                        'sUBL','sUBC1','sUBC2','sUBC3','sUBC4','sDRT1','sDRL','sDRC1','sDRC2','sDRC3','sDRC4','sDRC5','sDRC6','sDRC7',
                        'sDRC8','sDRC9','sDRC10','sDRC11','sDRC12','sDRC13','sDRC14','sDRC15','sDGT1','sDGL','sDGC1','sDGC2','sDGC3',
                        'sDGC4','sDGC5','sDGC6','sDGC7','sDGC8','sDGC9','sDGC10','sDGC11','sDGC12','sDGC13','sDGC14','sDGC15','sDBT',
                        'sDBL','sDBC1','sDBC2','sDBC3','sDBC4'
                    ]);
                    return $data_only;
                });
                $sheet->rows($rows);
            });
        })->store('csv',false,true);*/
        $head=array(
            'UpDates','sMT','sMS','sTM','sLI','sURT1','sURL','sURC1','sURC2','sURC3','sURC4','sURC5','sURC6','sURC7',
            'sURC8','sURC9','sURC10','sURC11','sURC12','sURC13','sURC14','sURC15','sUGT1','sUGL','sUGC1','sUGC2','sUGC3',
            'sUGC4','sUGC5','sUGC6','sUGC7','sUGC8','sUGC9','sUGC10','sUGC11','sUGC12','sUGC13','sUGC14','sUGC15','sUBT',
            'sUBL','sUBC1','sUBC2','sUBC3','sUBC4','sDRT1','sDRL','sDRC1','sDRC2','sDRC3','sDRC4','sDRC5','sDRC6','sDRC7',
            'sDRC8','sDRC9','sDRC10','sDRC11','sDRC12','sDRC13','sDRC14','sDRC15','sDGT1','sDGL','sDGC1','sDGC2','sDGC3',
            'sDGC4','sDGC5','sDGC6','sDGC7','sDGC8','sDGC9','sDGC10','sDGC11','sDGC12','sDGC13','sDGC14','sDGC15','sDBT',
            'sDBL','sDBC1','sDBC2','sDBC3','sDBC4'
        );
        $snu = $request->get('snu');
        $date1=$request->get('date1');
        $date2=$request->get('date2');
        $status=EquStatus::whereRaw("sNu='$snu' and UpDates Between '$date1' and '$date2'")->get()->toArray();
        $data=array_only($status,[
            'UpDates','sMT','sMS','sTM','sLI','sURT1','sURL','sURC1','sURC2','sURC3','sURC4','sURC5','sURC6','sURC7',
            'sURC8','sURC9','sURC10','sURC11','sURC12','sURC13','sURC14','sURC15','sUGT1','sUGL','sUGC1','sUGC2','sUGC3',
            'sUGC4','sUGC5','sUGC6','sUGC7','sUGC8','sUGC9','sUGC10','sUGC11','sUGC12','sUGC13','sUGC14','sUGC15','sUBT',
            'sUBL','sUBC1','sUBC2','sUBC3','sUBC4','sDRT1','sDRL','sDRC1','sDRC2','sDRC3','sDRC4','sDRC5','sDRC6','sDRC7',
            'sDRC8','sDRC9','sDRC10','sDRC11','sDRC12','sDRC13','sDRC14','sDRC15','sDGT1','sDGL','sDGC1','sDGC2','sDGC3',
            'sDGC4','sDGC5','sDGC6','sDGC7','sDGC8','sDGC9','sDGC10','sDGC11','sDGC12','sDGC13','sDGC14','sDGC15','sDBT',
            'sDBL','sDBC1','sDBC2','sDBC3','sDBC4'
        ]);
        $this->putCsv($head,$status);
    }
     function putCsv(array $head, $data, $mark = 'attack_ip_info', $fileName = "test.csv")
    {
        set_time_limit(0);
        $sqlCount = $data->count();
        // 输出Excel文件头，可把user.csv换成你要的文件名
        header('Content-Type: application/vnd.ms-excel;charset=utf-8');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $sqlLimit = 100000;//每次只从数据库取100000条以防变量缓存太大
        // 每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
        $limit = 100000;
        // buffer计数器
        $cnt = 0;
        $fileNameArr = array();
        // 逐行取出数据，不浪费内存
        for ($i = 0; $i < ceil($sqlCount / $sqlLimit); $i++) {
            $fp = fopen($mark . '_' . $i . '.csv', 'w'); //生成临时文件
            //     chmod('attack_ip_info_' . $i . '.csv',777);//修改可执行权限
            $fileNameArr[] = $mark . '_' .  $i . '.csv';
            // 将数据通过fputcsv写到文件句柄
            fputcsv($fp, $head);
            $dataArr = $data->offset($i * $sqlLimit)->limit($sqlLimit)->get()->toArray();
            foreach ($dataArr as $a) {
                $cnt++;
                if ($limit == $cnt) {
                    //刷新一下输出buffer，防止由于数据过多造成问题
                    ob_flush();
                    flush();
                    $cnt = 0;
                }
                fputcsv($fp, $a);
            }
            fclose($fp);  //每生成一个文件关闭
        }
        //进行多个文件压缩
        $zip = new ZipArchive();
        $filename = $mark . ".zip";
        $zip->open($filename, ZipArchive::CREATE);   //打开压缩包
        foreach ($fileNameArr as $file) {
            $zip->addFile($file, basename($file));   //向压缩包中添加文件
        }
        $zip->close();  //关闭压缩包
        foreach ($fileNameArr as $file) {
            unlink($file); //删除csv临时文件
        }
        //输出压缩文件提供下载
        header("Cache-Control: max-age=0");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename=' . basename($filename)); // 文件名
        header("Content-Type: application/zip"); // zip格式的
        header("Content-Transfer-Encoding: binary"); //
        header('Content-Length: ' . filesize($filename)); //
        @readfile($filename);//输出文件;
        unlink($filename); //删除压缩包临时文件
    }
}
