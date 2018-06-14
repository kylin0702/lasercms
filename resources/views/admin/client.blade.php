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
                <input type="text" class="form-control" name="phone" placeholder="请输入联系电话">&nbsp;&nbsp;
                <label for="review"><i class="fa fa-check"></i>审核状态</label>
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
           <h4 class="box-title" ><a data-widget="collapse" data-clientid="{{$v->ID}}">{{$v->ClientName}}</a></h4>
            @if ($v->Review!="已审核"&&Admin::user()->isRole('administrator'));
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
                <div class="col-lg-2">
                </div>
                <div class="col-lg-2">
                </div>
            </div>
            <div class="row">
                <div class="col-lg-4">
                    <a href="/admin/clients/{{$v->ID}}/edit" class="btn btn-sm btn-info">修改信息</a>
                    <a href="/admin/equipments/create?cid={{$v->ID}}" class="btn btn-sm btn-success">添加光源</a>
                </div>
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
                            <option value={{$a->username}}>{{$a->username}}</option>
                        @endforeach
                    </select>
                     <button type="button" class="btn btn-sm btn-warning btn-bindAgent" data-clientid="{{$v->ID}}">关联 <i class="fa fa-spin fa-spinner hidden"></i></button>
                        <button type="button" class="btn btn-sm btn-danger btn-bindAgentCancel">取消</button>
                    </span>
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
                var reviewtime=new Date(e.ReviewTime);//最后通讯时间
                var now=new Date();
                reviewtime=reviewtime.getTime();//转时间戳
                now=now.getTime();
                isOvertime=(reviewtime+600000)<now;//10分钟不通讯显示超时
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
                equipment += "<td>" + e.has_one_equ_type.Name + "</td>"
                equipment += "<td>" + e.RemainTime + "</td>"
                equipment += "<td>" + status + "</td>";
                equipment += "<td>" + e.ReviewTime + "</td>";
                equipment += "<td>@if(Admin::user()->inRoles(['administrator']))<a href='"+href1+"' class='btn btn-sm btn-success '>充值</a> " +
                                " <a href='"+href2+"' class='btn btn-sm btn-warning'>赠送</a>"+
                                " <a href='javascript:void(0);' class='btn btn-sm btn-danger btn-del' data-eid='"+e.ID+"' >删除</a>@endif</td>";
                equipment += "</tr>";
            });
            content.html(equipment);
            //删除光源
            $('.btn-del').on('click',function(){
                var eid=$(this).attr("data-eid");
                if(confirm("是否确定删除本台光源")){
                    $.ajax({
                        url:"/admin/equipments/"+eid,
                        method:"delete",
                        success:function (data) {
                            if(data.status){
                                alert(data.message);
                                collapse.trigger('click');
                                window.setTimeout(function () {
                                    collapse.trigger('click');
                                },1000);
                            }
                        }
                    });
                }
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

//绑定工程师操作
$(".btn-bindEngineer").on('click',function () {
    var clientid=$(this).attr("data-clientid");
    var engineer=$(".select-engineer").val();
    $(this).find("i").removeClass("hidden");
    $.post("/admin/clients/"+clientid+"/bindEngineer",{username:engineer},function(data){
        if(data.engineer==engineer){
            alert("绑定成功!");
            window.location.reload();
        }
    },"json");
})
//绑定用户操作
$(".btn-bindAgent").on('click',function () {
    var clientid=$(this).attr("data-clientid");
    var agent=$(".select-agent").val();
    $.post("/admin/clients/"+clientid+"/bindAgent",{username:agent})
},"json")
</script>