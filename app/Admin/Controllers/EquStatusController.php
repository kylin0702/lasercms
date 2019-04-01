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
    public function getShock(Request $request){
        $snu = $request->get('s');
        $status=EquStatus::where('sNU',"=", $snu)->orderby('ID','desc')->first();
        if(empty($status->sSCR1)){
            return 0;
        }
        else{
            //8个振幕字段由16进制转为2进制，全部拼接返回,第1位补"1"，防止转为二进制不足8位
            $sSCR1=base_convert("1".$status->sSCR1,16,2);
            $sSCR2=base_convert("1".$status->sSCR2,16,2);
            $sSCR3=base_convert("1".$status->sSCR3,16,2);
            $sSCR4=base_convert("1".$status->sSCR4,16,2);
            $sSCR5=base_convert("1".$status->sSCR5,16,2);
            $sSCR6=base_convert("1".$status->sSCR6,16,2);
            $sSCR7=base_convert("1".$status->sSCR7,16,2);
            $sSCR8=base_convert("1".$status->sSCR8,16,2);
            //把左边多出的1去掉,这样便是8个32bit二进制数组
            return array(substr($sSCR1,1),ltrim($sSCR2,1),ltrim($sSCR3,1),ltrim($sSCR4,1),ltrim($sSCR5,1),ltrim($sSCR6,1),ltrim($sSCR7,1),ltrim($sSCR8,1));
        }

    }
    //通过客户ID返回光源
    public function exportExcel(Request $request)
    {
        $filename = "光源状态记录(" . $request->get('snu') . ")" . rand(100, 999);//文件名:月份+月时长使用报表+当前日期+3位随机数
        return Excel::create($filename, function ($excel) use ($request) {
            $snu = $request->get('snu');
            $date1 = $request->get('date1');
            $date2 = $request->get('date2');
            $excel->sheet($snu, function ($sheet) use ($snu, $date1, $date2) {
                $status = EquStatus::whereRaw("sNu='$snu' and UpDates Between '$date1' and '$date2'")->get()->toArray();
                $sheet->row(1, array(
                    'sMT', 'sMS', 'sTM', 'sLI', 'sURT1', 'sURL', 'sURC1', 'sURC2', 'sURC3', 'sURC4', 'sURC5', 'sURC6', 'sURC7',
                    'sURC8', 'sURC9', 'sURC10', 'sURC11', 'sURC12', 'sURC13', 'sURC14', 'sURC15', 'sUGT1', 'sUGL', 'sUGC1', 'sUGC2', 'sUGC3',
                    'sUGC4', 'sUGC5', 'sUGC6', 'sUGC7', 'sUGC8', 'sUGC9', 'sUGC10', 'sUGC11', 'sUGC12', 'sUGC13', 'sUGC14', 'sUGC15', 'sUBT',
                    'sUBL', 'sUBC1', 'sUBC2', 'sUBC3', 'sUBC4', 'sDRT1', 'sDRL', 'sDRC1', 'sDRC2', 'sDRC3', 'sDRC4', 'sDRC5', 'sDRC6', 'sDRC7',
                    'sDRC8', 'sDRC9', 'sDRC10', 'sDRC11', 'sDRC12', 'sDRC13', 'sDRC14', 'sDRC15', 'sDGT1', 'sDGL', 'sDGC1', 'sDGC2', 'sDGC3',
                    'sDGC4', 'sDGC5', 'sDGC6', 'sDGC7', 'sDGC8', 'sDGC9', 'sDGC10', 'sDGC11', 'sDGC12', 'sDGC13', 'sDGC14', 'sDGC15', 'sDBT',
                    'sDBL', 'sDBC1', 'sDBC2', 'sDBC3', 'sDBC4','UpDates','sTMP1','sTMP2','sTMP3','sTMP4','sTMP5','sTMP6','sSCR1','sSCR2',
                    'sSCR3','sSCR4','sSCR5','sSCR6','sSCR7','sSCR8'
                ));
                $rows = collect($status)->map(function ($item) {
                    $data_only = array_only($item, [
                        'sMT', 'sMS', 'sTM', 'sLI', 'sURT1', 'sURL', 'sURC1', 'sURC2', 'sURC3', 'sURC4', 'sURC5', 'sURC6', 'sURC7',
                        'sURC8', 'sURC9', 'sURC10', 'sURC11', 'sURC12', 'sURC13', 'sURC14', 'sURC15', 'sUGT1', 'sUGL', 'sUGC1', 'sUGC2', 'sUGC3',
                        'sUGC4', 'sUGC5', 'sUGC6', 'sUGC7', 'sUGC8', 'sUGC9', 'sUGC10', 'sUGC11', 'sUGC12', 'sUGC13', 'sUGC14', 'sUGC15', 'sUBT',
                        'sUBL', 'sUBC1', 'sUBC2', 'sUBC3', 'sUBC4', 'sDRT1', 'sDRL', 'sDRC1', 'sDRC2', 'sDRC3', 'sDRC4', 'sDRC5', 'sDRC6', 'sDRC7',
                        'sDRC8', 'sDRC9', 'sDRC10', 'sDRC11', 'sDRC12', 'sDRC13', 'sDRC14', 'sDRC15', 'sDGT1', 'sDGL', 'sDGC1', 'sDGC2', 'sDGC3',
                        'sDGC4', 'sDGC5', 'sDGC6', 'sDGC7', 'sDGC8', 'sDGC9', 'sDGC10', 'sDGC11', 'sDGC12', 'sDGC13', 'sDGC14', 'sDGC15', 'sDBT',
                        'sDBL', 'sDBC1', 'sDBC2', 'sDBC3', 'sDBC4','sTMP1','UpDates','sTMP1','sTMP2','sTMP3','sTMP4','sTMP5','sTMP6','sSCR1','sSCR2',
                        'sSCR3','sSCR4','sSCR5','sSCR6','sSCR7','sSCR8'
                    ]);
                    return $data_only;
                });
                $sheet->rows($rows);
            });
        })->export('xlsx');
    }
}