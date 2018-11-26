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
                <label for="name"><i class="fa fa-user"></i> 客户名称</label>
                <input type="text" class="form-control" name="name" placeholder="请输入客户名称">&nbsp;&nbsp;
                <label for="phone"><i class="fa fa-map-marker"></i> 地址</label>
                <input type="text" class="form-control"  name="address" placeholder="请输入地址">&nbsp;&nbsp;
                <label for="review"><i class="fa fa-check"></i> 审核状态</label>
                <select name="review">
                    <option value="">全部</option>
                    <option value="未审核">未审核</option>
                    <option value="已审核">已审核</option>
                </select>&nbsp;&nbsp;
            </div>
            <button type="submit" class="btn btn-success">搜索</button>&nbsp;
            <a href="/admin/clients" type="button" class="btn btn-default">取消</a>
        </form>
    </div>
</div>
@foreach ($clients as $v)
    <div class="box box-default collapsed-box">
        <div class="box-header with-border">
           <h4 class="box-title" ><a data-widget="collapse" data-clientid="{{$v->ID}}" id="h4a{{$v->ID}}">{{$v->ClientName}}</a></h4>
            @if ($v->Review!="已审核"&&Admin::user()->isRole('administrator'))
            <div class="box-tools pull-right">
                <a href="/admin/clients/{{$v->ID}}/audit" class="btn btn-sm btn-success" type="button">审核</a>
            </div>
             @endif
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
                <div class="col-lg-2"><i class="fa fa-user-md"></i> 负责工程师:{{empty($v->hasOneEngineer)?"未绑定":$v->hasOneEngineer->name}}</div>
                <div class="col-lg-2">
                    <i class="fa fa-sitemap"></i>  代理商:{{empty($v->hasOneAgent)?"未绑定":$v->hasOneAgent->name}}
                </div>
                <div class="col-lg-4">
                    <i class="fa fa-user-secret"></i>  销售人员:{{empty($v->hasOneSeller)?"未绑定":$v->hasOneSeller->name}}
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <a  class="btn btn-sm btn-outline-success btn-engineer">绑定工程师</a>
                    <span class="span-engineer hidden">
                        <label>选择工程师:</label>
                    <select class="select-engineer">
                        @foreach ($engineer as $e)
                            <option value={{$e->username}}>{{$e->name}}</option>
                        @endforeach
                    </select>
                     <button type="button" class="btn btn-sm btn-warning btn-bindEngineer" data-clientid="{{$v->ID}}">绑定 <i class="fa fa-spin fa-spinner hidden"></i></button>
                        <button type="button" class="btn btn-sm btn-danger btn-bindEngineerCancel">取消</button>
                    </span>
                </div>
                <div class="col-lg-4">
                    <a  class="btn btn-sm btn-outline-success btn-agent">关联代理商</a>
                    <span class="span-agent hidden">
                        <label>选择代理商:</label>
                    <select class="select-agent">
                        @foreach ($agent as $a)
                            <option value='{{$a->username}}'>{{$a->name}}</option>
                        @endforeach
                    </select>
                     <button type="button" class="btn btn-sm btn-warning btn-bindAgent" data-clientid="{{$v->ID}}">关联 <i class="fa fa-spin fa-spinner hidden"></i></button>
                        <button type="button" class="btn btn-sm btn-danger btn-bindAgentCancel">取消</button>
                    </span>
                </div>
                <div class="col-lg-4">
                    <a  class="btn btn-sm btn-outline-success btn-seller">绑定销售人员</a>
                    <span class="span-seller hidden">
                        <label>选择销售人员:</label>
                    <select class="select-seller">
                        @foreach ($seller as $s)
                            <option value={{$s->username}}>{{$s->name}}</option>
                        @endforeach
                    </select>
                     <button type="button" class="btn btn-sm btn-warning btn-bindSeller" data-clientid="{{$v->ID}}">绑定 <i class="fa fa-spin fa-spinner hidden"></i></button>
                        <button type="button" class="btn btn-sm btn-danger btn-bindSellerCancel">取消</button>
                    </span>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <a href="/admin/clients/{{$v->ID}}/edit" class="btn btn-sm btn-info">修改信息</a>
                    <a href="/admin/equipments/create?cid={{$v->ID}}" class="btn btn-sm btn-success">添加光源</a>
                </div>
                <div class="col-lg-4 hidden">
                    <select class="custom-select month-select">
                        <option value="1">1月</option><option value="2">2月</option><option value="3">3月</option><option value="4">4月</option>
                        <option value="5">5月</option><option value="6" selected>6月</option><option value="7">7月</option> <option value="8">8月</option>
                        <option value="9">9月</option><option value="10">10月</option> <option value="11">11月</option><option value="12">12月</option>
                    </select>
                    <a class="btn btn-xs btn-adn btn-export" href="javascript:void(0)" target="_blank" data-clientid="{{$v->ID}}"><i class="fa fa-file-excel-o"></i>导出各厅月使用时长报表</a> (统计时间段：上月26日-本月25日)
                </div>
                <div class="col-lg-4">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <table class="table table-responsive table-bordered table-condensed">
                        <thead>
                        <th><input type="checkbox" class="allcheck"/></th>
                        <th>资产编号</th>
                        <th>厅号</th>
                        <th>光源序列</th>
                        <th>光源型号</th>
                        <th>销售类型</th>
                        <th>剩余时长</th>
                        <th>今年使用时长</th>
                        <th>光源状态</th>
                        <th>最后通讯时间</th>
                        <th>距离当前时长</th>
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
    <div class="modal-dialog" role="document" style="width: 1400px;">
        <div class="modal-content" id="app">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body" id="status-table">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<!-- 状态Modal -->
<div class="modal fade" id="myMiniModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" >
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">更换光源编号</h4>
            </div>
            <div class="modal-body" id="equnum-form">
                <div class="form-group">
                    <label class=" control-label col-sm-3 ">旧光源编号:</label>
                    <div class="input-group col-sm-7"> <b id="oldnum"></b></div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3">新光源编号:</label>
                    <div class="input-group col-sm-7"> <input id="newnum" class="form-control" /></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-success" id="btn-changeEquipment"><b>更换</b> <i class="fa fa-spinner fa-spin hidden "></i></button>
            </div>
        </div>
    </div>
</div>
<!-- 状态Modal -->
<!-- 删除光源的短信验证Modal -->
<div class="modal fade" id="delModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" >
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">删除光源</h4>
            </div>
            <div class="modal-body" id="equnum-form">
                <div class="form-group">
                    <label class=" control-label col-sm-3 ">验证码1:</label>
                    <div class="input-group col-sm-3"><input id="phone1" class="form-control" /></div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3">验证码2:</label>
                    <div class="input-group col-sm-3"> <input id="phone2"  class="form-control hidden" /></div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3"></label>
                    <div class="input-group col-sm-3"><button type='button'  class='btn btn-primary pull-left sms'>发送验证码</button></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-danger" disabled="disabled" id="btn-confirmDel"><b>删除光源</b></button>
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
                var isbuy="租赁";
                if(e.ISBuy!="否"){
                    if(e.ISBuy=="是"){isbuy="销售";}else{isbuy="测试"}
                }
                var href1="/admin/recharges/create?cid="+e.ClientID+"&eid="+e.ID+"&method=0";
                var href2="/admin/recharges/create?cid="+e.ClientID+"&eid="+e.ID+"&method=1";
                var remainTime=parseInt(e.RemainTime)+parseInt(e.GiftTime);//剩余时间等购买时间与赠送时间
                var reviewtime=new Date(e.ReviewTime);//最后通讯时间
                var now=new Date();
                var assetno=" ";
                if(e.AssetNo!=null){assetno=e.AssetNo;}
                reviewtime=reviewtime.getTime();//转时间戳
                now=now.getTime();
                isOvertime=(reviewtime+240000)<now;//4分钟不通讯显示超时
                overtime=Math.floor((now-reviewtime)/1000/60);
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
                equipment+="<td><input type='checkbox' name='eid' value='"+e.ID+"'/></td>";
                equipment += "<td>" + assetno + "</td>";
                equipment += "<td>" + e.NumBer + "</td>";
                equipment += "<td>" + e.EquNum + "</td>";
                equipment += "<td>" + e.has_one_equ_type.Name + "</td>"
                equipment += "<td>" +isbuy + "</td>"
                equipment += "<td>" + remainTime + "</td>"
                equipment += "<td>" + e.YearTotal + "</td>"
                equipment += "<td>" + status + "</td>";
                equipment += "<td>" + e.ReviewTime + "</td>";
                equipment += "<td>" +formatMinutes(overtime) + "</td>";
                equipment += "<td>@if(Admin::user()->inRoles(['administrator']))<a href='"+href1+"' class='btn btn-sm btn-success '>充值</a>&nbsp;&nbsp;" +
                                "<a href='"+href2+"' class='btn btn-sm btn-warning'>赠送</a>&nbsp;&nbsp;"+
                                "<a href='javascript:void(0);' class='btn btn-sm btn-danger btn-del' data-toggle='modal' data-target='#delModal' data-room='"+e.NumBer+"' data-cid='"+e.ClientID+"'  data-eid='"+e.ID+"' >删除</a>&nbsp;&nbsp;"+
                                "<a href='/admin/equipments/"+e.ID+"/edit' class='btn btn-sm btn-microsoft' >修改</a>&nbsp;&nbsp;"+
                                "<a href='javascript:void(0);' class='btn btn-sm btn-info' data-toggle='modal' data-target='#myModal'  data-snu='"+e.EquNum+"' onclick='getStatus(this)'>详细状态</a>&nbsp;&nbsp;"+
                                 "<a href='javascript:void(0);' class='btn btn-sm btn-github btn-changeEqu' data-target='#myMiniModal' data-toggle='modal' data-snu='"+e.EquNum+"' data-eid='"+e.ID+"' onclick='changeEquNum(this)'>更换光源</a>@endif</td>";
                equipment += "</tr>";

            });
            equipment+="@if(Admin::user()->inRoles(['administrator']))<tr><td colspan='12'> <a href='javascript:void(0)' class='btn btn-sm btn-danger btn-batchCharge' data-cid="+ClientID+" >批量充值</a></td></tr>@endif";
            content.html(equipment);
            //删除光源
            $('.btn-del').on('click',function(){
                var that=this;
                var codes=[];
                var eid=$(that).attr("data-eid");
                $('.sms').on('click',function(){
                    var clientid=$(that).attr("data-cid");
                    var clientname=$("#h4a"+clientid).html();
                    var room=$(that).attr("data-room");
                    $.get('/admin/equipments/sms',{clientname:clientname,room:room},function(data){
                        if(data){
                            for(var key in data){
                                codes.push(data[key]);
                            }
                            //alert("验证码为"+codes[0]+","+codes[1]+","+codes[2]);
                            alert("验证码已发送");
                        }
                        else{
                            alert("发送失败!");
                        }
                    });
                    $(this).attr('disabled','disabled');
                });
                $('#phone1').on('input propertychange',function(){
                    if(!codes.length==0){
                        codes=$.map(codes,function(n){
                            if( $('#phone1').val()==n) {
                                $('.check1').removeClass('hidden');
                                $('#phone1').attr('disabled',"disabled");
                                $('#phone2').removeClass('hidden');
                                return null;
                            }
                            else{
                                return n;
                            }

                        });
                    };
                });
                $('#phone2').on('input propertychange',function(){
                    if(!codes.length==0){
                        codes=$.map(codes,function(n){
                            if($('#phone2').val()==n) {
                                $('#phone2').attr('disabled',"disabled");
                                $("#btn-confirmDel").removeAttr("disabled");
                                return null;
                            }
                            else{
                                return n;
                            }

                        });
                    }
                });
                $('#btn-confirmDel').on('click',function(){
                    $.ajax({
                        url:"/admin/equipments/"+eid,
                        method:"delete",
                        success:function (data) {
                            if(data.status){
                                alert(data.message);
                                $("#delModal").find(".close").trigger('click');
                                collapse.trigger('click');
                                window.setTimeout(function () {
                                    collapse.trigger('click');
                                },1000);
                            }
                        }
                    });
                });
            });
            //获取设备ID,跳转至批量充值界面
           $('.btn-batchCharge').on('click',function () {
                var cid=$(this).attr("data-cid");
                var eids=[];
                var checks=$(this).parents('table').find('input:checkbox[name="eid"]:checked');
                $(checks).each(function (i,e) {
                    eids.push($(e).val());
                })
                if(eids.length==0){
                    alert("请勾选要充值的光源");
                }
                else{
                   window.location.href="/admin/recharges/batchCreate?cid="+cid+"&eids="+eids.join(',');
                }
            });
            //全选全不选
            $('.allcheck').on('click',function () {
                $(this).parents('table').find('input:checkbox[name="eid"]').prop("checked",$(this).prop("checked"));
            });
        }, "json");

});
//显示绑定工程师选择框
$(".btn-engineer").on('click',function () {
    $('.span-engineer').removeClass("hidden");
});
//隐藏绑定工程师选择框
$(".btn-bindEngineerCancel").on('click',function () {
    $('.span-engineer').addClass("hidden");
});

//显示绑定代理商选择框
$(".btn-agent").on('click',function () {
    $('.span-agent').removeClass("hidden");
});
//隐藏绑定代理商选择框
$(".btn-bindAgentCancel").on('click',function () {
    $('.span-agent').addClass("hidden");
});

//显示绑定销售人员选择框
$(".btn-seller").on('click',function () {
    $('.span-seller').removeClass("hidden");
});
//隐藏绑定销售人员选择框
$(".btn-bindSellerCancel").on('click',function () {
    $('.span-seller').addClass("hidden");
});



//绑定工程师操作
$(".btn-bindEngineer").on('click',function () {
    var clientid=$(this).attr("data-clientid");
    var engineer=$(this).parent().find(".select-engineer").val();
    $(this).find("i").removeClass("hidden");
    $.post("/admin/clients/"+clientid+"/bindEngineer",{username:engineer},function(data){
        if(data==engineer){
            alert("绑定成功!");
            window.location.reload();
        }
    },"json");
})
//绑定代理操作
$(".btn-bindAgent").on('click',function () {
    var clientid = $(this).attr("data-clientid");
    var agent =$(this).parent().find(".select-agent").val();
    $(this).find("i").removeClass("hidden");
    $.post("/admin/clients/" + clientid + "/bindAgent", {username: agent}, function (data) {
        if (data== agent) {
            alert("绑定成功!");
            window.location.reload();
        }
    }, "json");
});
//绑定销售人员操作
$(".btn-bindSeller").on('click',function () {
    var clientid = $(this).attr("data-clientid");
    var seller =$(this).parent().find(".select-seller").val();
    $(this).find("i").removeClass("hidden");
    $.post("/admin/clients/" + clientid + "/bindSeller", {username: seller}, function (data) {
        if (data== seller) {
            alert("绑定成功!");
            window.location.reload();
        }
    }, "json");
});
//导出选择月份的使用时长报表
$('.btn-export').on('click',function(){
    var clientid=$(this).attr("data-clientid");
    var month=$(this).prev(".month-select").val();
    window.open("/admin/recharges/exportExcel?clientid="+clientid+"&month="+month);
});
//时间格式化
function formatMinutes(StatusMinute){
    var day=parseInt(StatusMinute/60/24);
    var hour=parseInt(StatusMinute/60%24);
    var min= parseInt(StatusMinute % 60);
    StatusMinute="";
    if (day > 0)
    {
        StatusMinute= day + "天";
    }
    if (hour>0)
    {
        StatusMinute += hour + "小时";
    }
    if (min>0)
    {
        StatusMinute += parseFloat(min) + "分钟";
    }
    return StatusMinute;
}

</script>
<script src="/vendor/templatejs/template.min.js"></script>
<script>
    function getStatus(a) {
        var snu=$(a).attr("data-snu");
        var sample={"ID":6221217,"sNU":"I00001170726","sMT":"CB10L-10","sMS":"LampOn","sTM":"1595","sLI":"900","sURT1":"1900","sURL":"255","sURC1":"755","sURC2":"900","sURC3":"900","sURC4":"900","sURC5":"900","sURC6":"900","sURC7":"900","sURC8":"900","sURC9":"900","sURC10":"900","sURC11":"900","sURC12":"900","sURC13":"900","sURC14":"900","sURC15":"900","sUGT1":"900","sUGL":"255","sUGC1":"555","sUGC2":"1900","sUGC3":"1900","sUGC4":"1900","sUGC5":"1900","sUGC6":"1900","sUGC7":"1900","sUGC8":"1900","sUGC9":"1900","sUGC10":"1900","sUGC11":"1900","sUGC12":"1900","sUGC13":"1900","sUGC14":"1900","sUGC15":"1900","sUBT":"1900","sUBL":"255","sUBC1":"355","sUBC2":"2500","sUBC3":"2500","sUBC4":"2500","sDRT1":"2500","sDRL":"12361","sDRC1":"00","sDRC2":"00","sDRC3":"00","sDRC4":"00","sDRC5":"00","sDRC6":"14592","sDRC7":"00","sDRC8":"1943","sDRC9":"13875","sDRC10":"00","sDRC11":"1024","sDRC12":"13362","sDRC13":"4","sDRC14":"16384","sDRC15":"256","sDGT1":"00","sDGL":"00","sDGC1":"00","sDGC2":"1","sDGC3":"1","sDGC4":"14080","sDGC5":"1","sDGC6":"14080","sDGC7":"4179","sDGC8":"41","sDGC9":"00","sDGC10":"00","sDGC11":"00","sDGC12":"00","sDGC13":"00","sDGC14":"57","sDGC15":"00","sDBT":"1943","sDBL":"580","sDBC1":"1","sDBC2":"00","sDBC3":"00","sDBC4":"4","UpDates":"2018-04-14 11:50:53.110"}
        var d;
        $.get('/admin/equstatuss/getStatus?s=' + snu, function (data) {
            var tpl = document.getElementById('test').innerHTML;
            var html = template(tpl, data);
            document.getElementById('status-table').innerHTML = html;
            $('#status-table tr td:nth-child(1)').css('color','red');
            $('#status-table tr td:nth-child(3)').css('color','red');
            $('#status-table tr td:nth-child(5)').css('color','green');
            $('#status-table tr td:nth-child(7)').css('color','green');
            $('#status-table tr td:nth-child(9)').css('color','blue');
            $('#status-table tr td:nth-child(11)').css('color','blue');
            $('.date1').datetimepicker({showClose:true,format:"YYYY-MM-DD",locale:"zh-CN",widgetPositioning:{horizontal: 'right',vertical:'bottom'}});
            $('.date2').datetimepicker({showClose:true,format:"YYYY-MM-DD",locale:"zh-CN",widgetPositioning:{horizontal: 'right',vertical:'bottom'}});
            $('.btn-exportStatus').on('click',function(){
                var date1=$(this).parents('table').find('.date1').val();
                var date2=$(this).parents('table').find('.date2').val();
                if(date1==""){alert('请输入开始日期');return false;}
                if(date2==""){alert('请输入结束日期');return false;}
                window.open("/admin/equstatuss/exportExcel?snu="+snu+"&date1="+date1+"&date2="+date2);
            });
        });
    }
    function changeEquNum(a){
        var eid=$(a).attr("data-eid");
        var snu=$(a).attr("data-snu");
        $("#oldnum").html(snu);
        $("#btn-changeEquipment").on('click',function(){
            var that=this;
            var newnum=$("#newnum").val();
            newnum=newnum.trim();
            if(newnum.length==12){
                $(that).find("b").html("处理中,请稍等...")
                $(that).find(".fa-spin").removeClass("hidden");
                $(that).attr("disabled","disabled");
                $.get("/admin/equipments/changeEquipment",{eid:eid,old:snu,new:newnum},function (callback) {
                    if(callback.result==false){
                        $(that).find("b").html("更换")
                        $(that).find(".fa-spin").addClass("hidden");
                        $(that).removeAttr("disabled");
                        sweetAlert(callback.message);
                        return false;
                    }
                    else{
                        $(that).attr("disabled","disabled");
                        $(that).html("更换完成");
                    }
                },"json");
            }
            else {
                sweetAlert("光源编号格式不对！")
            }
        });
    }
</script>
<script id="test" type="text/html">
                <table class="table table-bordered table-responsive table-condensed">
                    <tbody>
                    <thead>
                    <tr><th>光源编号</th><th colspan="2"><%=sNU%></th><th  colspan="1">开始日期：</th><th colspan="2"><input type="text" class="form-control date1"></th><th  colspan="1">结束日期：</th><th  colspan="2"><input type="text" class="form-control date2"></th><th colspan="4"><a  href='javascript:void(0)' class="btn btn-sm btn-success btn-exportStatus" >导出所选时间段数据</a></th></tr>
                    </thead>
                    <tr class="hidden">
                        <td>上红光模组功率</td> <td><%=sURL%></td><td>下红光模组功率</td><td><%=sDRL%></td><td>上绿光模组功率</td><td><%=sURL%></td><td>下绿光模组功率</td><td><%=sDGL%></td><td>上蓝光模组功率</td><td><%=sUBL%></td><td>下蓝光模组功率</td><td><%=sDBL%></td>
                    </tr>
                    <tr class="hidden">
                        <td>上红光模组温度</td> <td><%=sURT1%></td><td>下红光模组温度</td><td><%=sDRT1%></td><td>上绿光模组温度</td><td><%=sUGT1%></td><td>下绿光模组温度</td><td><%=sDGT1%></td><td>上蓝光模组温度</td><td><%=sUBT%></td><td>下蓝光模组温度</td><td><%=sDBT%></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流1</td> <td><%=sURC1%></td><td>下红光模组电流1</td><td><%=sDRC1%></td><td>上绿光模组电流1</td><td><%=sUGC1%></td><td>下绿光模组电流1</td><td><%=sDGC1%></td><td>上蓝光模组电流1</td><td><%=sUBC1%></td><td>下蓝光模组电流1</td><td><%=sDBC1%></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流2</td> <td><%=sURC2%></td><td>下红光模组电流2</td><td><%=sDRC2%></td><td>上绿光模组电流2</td><td><%=sUGC2%></td><td>下绿光模组电流2</td><td><%=sDGC2%></td><td>上蓝光模组电流2</td><td><%=sUBC2%></td><td>下蓝光模组电流2</td><td><%=sDBC2%></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流3</td> <td><%=sURC3%></td><td>下红光模组电流3</td><td><%=sDRC3%></td><td>上绿光模组电流3</td><td><%=sUGC3%></td><td>下绿光模组电流3</td><td><%=sDGC3%></td><td>上蓝光模组电流3</td><td><%=sUBC3%></td><td>下蓝光模组电流3</td><td><%=sDBC3%></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流4</td> <td><%=sURC4%></td><td>下红光模组电流4</td><td><%=sDRC4%></td><td>上绿光模组电流4</td><td><%=sUGC4%></td><td>下绿光模组电流4</td><td><%=sDGC4%></td><td>上蓝光模组电流4</td><td><%=sUBC4%></td><td>下蓝光模组电流4</td><td><%=sDBC4%></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流5</td> <td><%=sURC5%></td><td>下红光模组电流5</td><td><%=sDRC5%></td><td>上绿光模组电流5</td><td><%=sUGC5%></td><td>下绿光模组电流5</td><td><%=sDGC5%></td><td>振幕1</td><td><%if(sSRC1){%><%=sSRC1%><%}%></td><td>温度1</td><td><%if(sTMP1){%><%=sTMP1%><%}%></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流6</td> <td><%=sURC6%></td><td>下红光模组电流6</td><td><%=sDRC6%></td><td>上绿光模组电流6</td><td><%=sUGC6%></td><td>下绿光模组电流6</td><td><%=sDGC6%></td><td>振幕2</td><td><%if(sSRC2){%><%=sSRC2%><%}%></td><td>温度2</td><td><%if(sTMP2){%><%=sTMP2%><%}%></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流7</td> <td><%=sURC7%></td><td>下红光模组电流7</td><td><%=sDRC7%></td><td>上绿光模组电流7</td><td><%=sUGC7%></td><td>下绿光模组电流7</td><td><%=sDGC7%></td><td>振幕3</td><td><%if(sSRC3){%><%=sSRC3%><%}%></td><td>温度3</td><td><%if(sTMP3){%><%=sTMP1%><%}%></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流8</td> <td><%=sURC8%></td><td>下红光模组电流8</td><td><%=sDRC8%></td><td>上绿光模组电流8</td><td><%=sUGC8%></td><td>下绿光模组电流8</td><td><%=sDGC8%></td><td>振幕4</td><td><%if(sSRC4){%><%=sSRC4%><%}%></td><td>温度4</td><td><%if(sTMP4){%><%=sTMP1%><%}%></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流9</td> <td><%=sURC9%></td><td>下红光模组电流9</td><td><%=sDRC9%></td><td>上绿光模组电流9</td><td><%=sUGC9%></td><td>下绿光模组电流9</td><td><%=sDGC9%></td><td>振幕5</td><td></td><%if(sSRC5){%><%=sSRC5%><%}%><td>温度5</td><td><%if(sTMP5){%><%=sTMP1%><%}%></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流10</td> <td><%=sURC10%></td><td>下红光模组电流10</td><td><%=sDRC10%></td><td>上绿光模组电流10</td><td><%=sUGC10%></td><td>下绿光模组电流10</td><td><%=sDGC10%></td><td>振幕6</td><td><%if(sSRC6){%><%=sSRC6%><%}%></td><td>温度6</td><td><%if(sTMP6){%><%=sTMP6%><%}%></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流11</td> <td><%=sURC11%></td><td>下红光模组电流11</td><td><%=sDRC11%></td><td>上绿光模组电流11</td><td><%=sUGC11%></td><td>下绿光模组电流11</td><td><%=sDGC11%></td><td>振幕7</td><td><%if(sSRC7){%><%=sSRC7%><%}%></td><td></td><td></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流12</td> <td><%=sURC12%></td><td>下红光模组电流12</td><td><%=sDRC12%></td><td>上绿光模组电流12</td><td><%=sUGC12%></td><td>下绿光模组电流12</td><td><%=sDGC12%></td><td>振幕8</td><td><%if(sSRC8){%><%=sSRC8%><%}%></td><td></td><td></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流13</td> <td><%=sURC13%></td><td>下红光模组电流13</td><td><%=sDRC13%></td><td>上绿光模组电流13</td><td><%=sUGC13%></td><td>下绿光模组电流13</td><td><%=sDGC13%></td><td></td><td></td><td></td><td></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流14</td> <td><%=sURC14%></td><td>下红光模组电流14</td><td><%=sDRC14%></td><td>上绿光模组电流14</td><td><%=sUGC14%></td><td>下绿光模组电流14</td><td><%=sDGC14%></td><td></td><td></td><td></td><td></td>
                    </tr>
                    <tr>
                        <td>上红光模组电流15</td> <td><%=sURC15%></td><td>下红光模组电流15</td><td><%=sDRC15%></td><td>上绿光模组电流15</td><td><%=sUGC15%></td><td>下绿光模组电流15</td><td><%=sDGC15%></td><td></td><td></td><td></td><td></td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr><th>总功率</th><th colspan="11"><%=sLI%></th></tr>
                    </tfoot>

                </table>

</script>