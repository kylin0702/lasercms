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
<div class="box box-danger">
    <div class="box-body">
        <form class="form-inline" action="/admin/clients" method="get">
            <div class="form-group">
                <label for="name"><i class="fa fa-user"></i>客户名称</label>
                <input type="text" class="form-control" name="name" placeholder="请输入客户名称">&nbsp;&nbsp;
                <label for="phone"><i class="fa fa-phone"></i>联系电话</label>
                <input type="text" class="form-control" name="phone" placeholder="请输入联系电话">&nbsp;&nbsp;&nbsp;
            </div>
            <button type="submit" class="btn btn-success">搜索</button>&nbsp;
            <a href="/admin/clients" type="button" class="btn btn-default">取消</a>
        </form>
    </div>
</div>
@foreach ($clients as $v)
<div class="box box-default collapsed-box">
    <div class="box-header with-border">
        <h4 class="box-title" ><a data-widget="collapse" data-clientid="{{$v->ID}}">{{$v->ClientName}}</a></h4>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-lg-2"><i class="fa fa-star"></i> 用户编号:{{$v->ClientNum}}</div>
            <div class="col-lg-2"><i class="fa fa-user"></i> 法人名称:{{$v->Owner}}</div>
            <div class="col-lg-2"><i class="fa fa-mobile"></i> 联系方式:{{$v->Phone}}</div>
            <div class="col-lg-2"><i class="fa fa-sitemap"></i> 所属区域:{{$v->hasOneArea->AreaName}}</div>
            <div class="col-lg-2"><i class="fa fa-calendar-check-o"></i>注册时间:{{date("Y-m-d",strtotime($v->UpdateTime))}}</div>
            <div class="col-lg-2"><i class="fa fa-check"></i>审核状态:{{$v->Review}}</div>
        </div>
        <div class="row">
            <div class="col-lg-4"><i class="fa fa-map-marker"></i> 客户地址:{{$v->Adress}}</div>
            <div class="col-lg-4 hidden">
                <select class="custom-select month-select">
                    <option value="1">1月</option><option value="2">2月</option><option value="3">3月</option><option value="4">4月</option>
                    <option value="5">5月</option><option value="6" selected>6月</option><option value="7">7月</option> <option value="8">8月</option>
                    <option value="9">9月</option><option value="10">10月</option> <option value="11">11月</option><option value="12">12月</option>
                </select>
                <a class="btn btn-xs btn-adn btn-export" href="javascript:void(0)" target="_blank" data-clientid="{{$v->ID}}"><i class="fa fa-file-excel-o"></i>导出各厅月使用时长报表</a> (统计时间段：上月26日-本月25日)
            </div>
            <div class="col-lg-2">
            </div>
            <div class="col-lg-2">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <table class="table table-responsive table-bordered table-condensed">
                    <thead>
                    <th>厅号</th>
                    <th>光源序列</th>
                    <th>光源型号</th>
                    <th>剩余时长</th>
                    <th>今年使用时长</th>
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
<!-- 状态Modal -->
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
                var href1="/admin/recharges/create?cid="+e.ClientID+"&eid="+e.ID+"&method=0";
                var href2="/admin/recharges/create?cid="+e.ClientID+"&eid="+e.ID+"&method=1";
                var remainTime=parseInt(e.RemainTime)+parseInt(e.GiftTime);//剩余时间等购买时间与赠送时间
                var reviewtime=moment(e.ReviewTime);//最后通讯时间
                var now=moment();
                reviewtime=reviewtime.unix();//转时间戳
                now=now.unix();
                isOvertime=(reviewtime+240)<now;//10分钟不通讯显示超时
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
                equipment += "<td>" + remainTime + "</td>";
                equipment += "<td>" + e.YearTotal + "</td>";
                equipment += "<td>" + e.TotalTime+ "小时</td>";
                equipment += "<td>" + status + "</td>";
                equipment += "<td>" + e.ReviewTime + "</td>";
                equipment += "<td><a href='javascript:void(0);' class='btn btn-sm btn-info btn-recharge' data-number='"+e.NumBer+"' data-eid='"+e.ID+"'>查看充值记录</a>  ";
                equipment += "</tr>";
            });
            content.html(equipment);
            //查看充值记录
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
    //导出选择月份的使用时长报表
    $('.btn-export').on('click',function(){
        var clientid=$(this).attr("data-clientid");
        var month=$(this).prev(".month-select").val();
        window.open("/admin/recharges/exportExcel?clientid="+clientid+"&month="+month);
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