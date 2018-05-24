<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Area;

use App\Http\Controllers\Controller;
use Encore\Admin\Form;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AreaController extends Controller
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

            $content->header('区域设置');
            $content->description('区域列表');

            $content->body(Area::tree(function($area){
                $area->disableSave();
                $area->branch(function ($branch) {
                    return "{$branch['AreaCode']}-{$branch['AreaName']}";
                });
            }));
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

            $content->header('修改区域');
            $content->description('');

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

            $content->header('新增区域');
            $content->description('');

            $content->body($this->form());
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Area::class, function (Form $form) {
                $form->text("AreaName","区域名称")->setWidth(2);
                $form->text("AreaCode","区域代码")->setWidth(2);
                $form->select("Superior","所属区域")->setWidth(2)->options(function (){
                    $superior=Area::where("Superior","=",0)->get();
                    $data=[0=>"根级区域"];
                    foreach ($superior as $item){
                        $data[$item["ID"]]=$item["AreaName"];
                    }
                    return $data;
                });
        });
    }

    //通过父级区域代码查询子区域,用于二级联动
    public function getSonArea(\Illuminate\Http\Request $request){
        $sid = $request->get('q');
        return Area::where('Superior',"=", $sid)->get(["ID",DB::raw("AreaCode+AreaName as AreaName")]);
    }
}
