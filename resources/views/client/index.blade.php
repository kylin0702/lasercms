<style>
    @-webkit-keyframes shake{
        0%{
            opacity: 1;
        }
        50%{
            opacity: 0.5;
        }
        100%{
            opacity: 1;
        }
    }@keyframes shake{
         0%{
             opacity: 1;
         }
         50%{
             opacity: 0.5;
         }
         100%{
             opacity: 1;
         }
     }
    .shake{
        -webkit-animation: shake 2s infinite;
        animation: shake 2s infinite;
    }
</style>
@foreach ($clients as $v)
    <div class="box box-default collapsed-box">
        <div class="box-header with-border">
            <h4 class="box-title" ><a data-widget="collapse" data-clientid="{{$v->ID}}">{{$v->ClientName}}</a></h4>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-lg-2"><i class="fa fa-star"></i> 用户编号:{{$v->ClientNum}}</div>
                <div class="col-lg-2"><i class="fa fa-user"></i> 联系人:{{$v->Owner}}</div>
                <div class="col-lg-2"><i class="fa fa-mobile"></i> 联系方式:{{$v->Phone}}</div>
                <div class="col-lg-2"><i class="fa fa-sitemap"></i> 所属区域:{{$v->hasOneArea->AreaName}}</div>
                <div class="col-lg-2"><i class="fa fa-calendar-check-o"></i>注册时间:{{date("Y-m-d",strtotime($v->UpdateTime))}}</div>
                <div class="col-lg-2"><a class="btn btn-xs btn-adn " href="/admin/recharges/exportExcel?clientid={{$v->ID}}" target="_blank"><i class="fa fa-file-excel-o"></i> 导出使用时长报表</a></div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-responsive table-bordered table-condensed">
                        <thead>
                        <th>厅号</th>
                        <th>光源序列</th>
                        <th>光源型号</th>
                        <th>剩余时长</th>
                        <th>累计充值</th>
                        <th>光源状态</th>
                        <th>最后通讯时间</th>
                        <th>操作</th>
                        </thead>
                        <tbody class="equipment"></tbody>
                    </table>
                </div>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
@endforeach
{!!$clients->links()!!}
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" >
        <div class="modal-content" id="app">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body" >
                <table id="recharge-table" class="table table-bordered table-responsive">
                    <thead>
                        <tr>
                            <th><i class="fa fa-clock-o"></i> 充值时间</th>
                            <th><i class="fa fa-camera-retro"></i> 充值时长</th>
                            <th><i class="fa fa-list"></i> 充值类型</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<script>
    $("[data-widget='collapse']").on('click',function(){
        var collapse=$(this);
        var ClientID=$(this).attr("data-clientid");
        var content=$(this).parents('.collapsed-box').find('.equipment');
        content.html("<tr><td colspan='6'><span class='shake'> 数据加载中... </span></td></tr>");
        $.get("/admin/equipments/getEquipment", {ClientID: ClientID}, function (data) {
            var equipment = "";
            $(data).each(function (i, e) {
                var status="";
                var remainTime=parseInt(e.RemainTime)+parseInt(e.GiftTime);//剩余时间等购买时间与赠送时间
                var reviewtime=new Date(e.ReviewTime);//最后通讯时间
                var now=new Date();
                reviewtime=reviewtime.getTime();//转时间戳
                now=now.getTime();
                isOvertime=(reviewtime+240000)<now;//10分钟不通讯显示超时
                if(!isOvertime){
                    switch (e.EquStatus) {
                        case "LampOn":
                            status="<label class='label label-success'>放映中</label><i class='fa fa-spin fa-cog'></i>";
                            break;
                        case "Unactive":
                            status="<label class='label label-default'>未激活</label>";
                            break;
                        case "Standby":
                            status="<label class='label label-info'>待机中</label>";
                            break;
                        default:
                            status="<label class='label label-info'>待机中</label>";
                            break;
                    }
                }
                else{
                    status="<label class='label label-default'>离线</label>";
                }
                equipment += "<tr>";
                equipment += "<td>" + e.NumBer + "</td>";
                equipment += "<td>" + e.EquNum + "</td>";
                equipment += "<td>" + e.has_one_equ_type.Name + "</td>";
                equipment += "<td>" + remainTime + "小时</td>";
                equipment += "<td>" + e.TotalTime+ "小时</td>";
                equipment += "<td>" + status + "</td>";
                equipment += "<td>" + e.ReviewTime + "</td>";
                equipment += "<td><a href='javascript:void(0);' class='btn btn-sm btn-info btn-recharge' data-number='"+e.NumBer+"' data-eid='"+e.ID+"'>查看充值记录</a>  ";
                equipment += "</tr>";
            });
            content.html(equipment);
            //删除光源
            $('.btn-recharge').on('click',function(){
                var eid=$(this).attr("data-eid");
                $('#myModalLabel').html($(collapse).html()+'-'+$(this).attr("data-number"))
                $.get('/admin/recharges/getRecharge',{eid:eid},function(result){
                    $("#recharge-table").find("tbody").html('');//清空表格内容
                    if(result.length>0){
                        $(result).each(function (i,e) {
                            var updatetime=new Date(e.UpdateTime.date);
                            updatetime=dateFtt("yyyy-MM-dd hh:mm:ss",updatetime);
                            var method="";
                            if(e.RechTime<0){
                                method="保底扣费";
                            }
                            else{
                                method=e.Method==0?"用户充值":"优惠赠送";
                            }
                            var content="<tr><td>"+updatetime+"</td>";
                                content+="<td>"+e.RechTime+"小时</td>";
                                content+="<td>"+method+"</td></tr>";
                            $("#recharge-table").find("tbody").append(content);
                        });
                    }
                });
                $('#myModal').modal();
            });
        }, "json");
    });
</script>
<script>
    /**************************************时间格式化处理************************************/
    function dateFtt(fmt,date)
    { //author: meizz
        var o = {
            "M+" : date.getMonth()+1,                 //月份
            "d+" : date.getDate(),                    //日
            "h+" : date.getHours(),                   //小时
            "m+" : date.getMinutes(),                 //分
            "s+" : date.getSeconds(),                 //秒
            "q+" : Math.floor((date.getMonth()+3)/3), //季度
            "S"  : date.getMilliseconds()             //毫秒
        };
        if(/(y+)/.test(fmt))
            fmt=fmt.replace(RegExp.$1, (date.getFullYear()+"").substr(4 - RegExp.$1.length));
        for(var k in o)
            if(new RegExp("("+ k +")").test(fmt))
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        return fmt;
    }
</script>