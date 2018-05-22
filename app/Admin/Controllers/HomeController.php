<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Client;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Callout;
use Encore\Admin\Widgets\InfoBox;
use function foo\func;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('信息面板');
            $content->description('');

            $content->row(function(Row $row){
                $row->column(3,function (Column $column){
                    $client=new Client();
                    $count=$client->count();
                    $box=new InfoBox("客户数量","user","info","http://www.qq.com","$count");
                    $column->append($box);
                });
                $row->column(3,function (Column $column){
                    $client=new Client();
                    $count=$client->count();
                    $box=new InfoBox("客户数量","user","info","http://www.qq.com","$count");
                    $column->append($box);
                });
                $row->column(3,function (Column $column){
                    $client=new Client();
                    $count=$client->count();
                    $box=new InfoBox("客户数量","user","info","http://www.qq.com","$count");
                    $column->append($box);
                });
                $row->column(3,function (Column $column){
                    $client=new Client();
                    $count=$client->count();
                    $box=new InfoBox("客户数量","user","info","http://www.qq.com","$count");
                    $column->append($box);
                });
            });
            /*
            $content->row(function (Row $row) {

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::environment());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::extensions());
                });

                $row->column(4, function (Column $column) {
                    $column->append(Dashboard::dependencies());
                });
            });
            */
        });
    }
    public function welcome(){
        return view("admin.index");
    }
}
