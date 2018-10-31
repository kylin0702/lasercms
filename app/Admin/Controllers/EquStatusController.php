<?php

namespace App\Admin\Controllers;

use App\admin\Models\EquStatus;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
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
        $filename="光源状态记录(".$request->get('s').")".rand(100,999);//文件名:月份+月时长使用报表+当前日期+3位随机数
        return Excel::create($filename, function($excel) use ($request) {
            $snu = $request->get('s');
            $date1=$request->get('date1');
            $date2=$request->get('date2');
            $excel->sheet($snu, function($sheet) use($snu,$date1,$date2){
                $sheet->setWidth(['A'=>10,'B'=>40,'C'=>40,'D'=>20]);
                $status=EquStatus::whereRaw("EquNum='$snu' and UpDates Between '$date1' and '$date2'")->get()->toArray();
                $sheet->row(1, array(
                    '光源编号','总功率','上红光模组功率','下红光模组功率'
                ));
                $rows = collect($status)->map(function ($item) {
                    $data_only=array_only($item,['sNu','sLI','sURL','sDRL']);
                    return $data_only;
                });
                $sheet->rows($rows);
            });
        })->export('xls');
    }
}
