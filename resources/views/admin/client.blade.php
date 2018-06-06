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
                <label for="cname"><i class="fa fa-user"></i>客户名称</label>
                <input type="text" class="form-control" name="cname" placeholder="请输入客户名称">
                <label for="cphone"><i class="fa fa-phone"></i>联系电话</label>
                <input type="text" class="form-control" name="cphone" placeholder="请输入联系电话">
            </div>
            <button type="submit" class="btn btn-success">搜索</button>
            <a href="/admin/clients" type="button" class="btn btn-default">取消</a>
        </form>
    </div>
</div>
@foreach ($clients as $v)
    <div class="box box-default collapsed-box">
        <div class="box-header with-border">
           <h4 class="box-title" ><a data-widget="collapse" data-clientid="{{$v->ID}}">{{$v->ClientName}}</a></h4>
        </div><!-- /.box-header -->
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
                <div class="col-lg-2"><i class="fa fa-link"></i> 关联用户:{{empty($v->username)?"未关联":$v->username}}</div>
                <div class="col-lg-2"><i class="fa fa-user-md"></i> 负责工程师:{{empty($v->hasOneEngineer)?"未绑定":$v->hasOneEngineer->name}}</div>
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
                    <a  class="btn btn-sm btn-outline-success btn-user">关联用户</a>
                    <span class="span-user hidden">
                        <label>选择用户:</label>
                    <select class="select-user">
                        @foreach ($user as $u)
                            <option value={{$u->username}}>{{$u->username}}</option>
                        @endforeach
                    </select>
                     <button type="button" class="btn btn-sm btn-warning btn-bindUser" data-clientid="{{$v->ID}}">关联 <i class="fa fa-spin fa-spinner hidden"></i></button>
                        <button type="button" class="btn btn-sm btn-danger btn-bindUserCancel">取消</button>
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
    var ClientID=$(this).attr("data-clientid");
    var content=$(this).parents('.collapsed-box').find('.equipment');
    if(content.text()=="") {
        content.html("<tr><td colspan='6'><span class='shake'> 数据加载中... </span></td></tr>");
        $.get("/admin/equipments/getEquipment", {ClientID: ClientID}, function (data) {
            var equipment = "";
            $(data).each(function (i, e) {
                var status="";
                var href1="/admin/recharges/create?cid="+e.ClientID+"&eid="+e.ID+"&method=0";
                var href2="/admin/recharges/create?cid="+e.ClientID+"&eid="+e.ID+"&method=1";
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
                equipment += "<tr>";
                equipment += "<td>" + e.NumBer + "</td>";
                equipment += "<td>" + e.EquNum + "</td>";
                equipment += "<td>" + e.has_one_equ_type.Name + "</td>"
                equipment += "<td>" + status + "</td>";
                equipment += "<td>" + e.ReviewTime + "</td>";
                equipment += "<td><a href='"+href1+"' class='btn btn-sm btn-success'>充值</a> " +
                                " <a href='"+href2+"' class='btn btn-sm btn-warning'>赠送</a></td>"+
                                " <a href='' class='btn btn-sm btn-danger'>删除</a></td>";
                equipment += "</tr>";
            });
            content.html(equipment);
        }, "json");
    }
});
//显示绑定工程师选择框
$(".btn-engineer").on('click',function () {
    $('.span-engineer').removeClass("hidden");
});
//隐藏绑定工程师选择框
$(".btn-bindEngineerCancel").on('click',function () {
    $('.span-engineer').addClass("hidden");
});

//显示绑定工程师选择框
$(".btn-user").on('click',function () {
    $('.span-user').removeClass("hidden");
});
//隐藏绑定用户选择框
$(".btn-bindUserCancel").on('click',function () {
    $('.span-user').addClass("hidden");
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
$(".btn-bindUser").on('click',function () {
    var clientid=$(this).attr("data-clientid");
    var user=$(".select-user").val();
    $.post("/admin/clients/"+clientid+"/bindUser",{username:user})
},"json")
</script>