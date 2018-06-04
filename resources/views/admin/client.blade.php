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
                <div class="col-lg-3"><i class="fa fa-user"></i> 法人名称:{{$v->Owner}}</div>
                <div class="col-lg-3"><i class="fa fa-mobile"></i> 联系方式:{{$v->Phone}}</div>
                <div class="col-lg-3"><i class="fa fa-sitemap"></i> 所属区域:{{$v->hasOneArea->AreaName}}</div>
                <div class="col-lg-3"><i class="fa fa-calendar-check-o"></i>注册时间:{{date("Y-m-d",strtotime($v->UpdateTime))}}</div>
            </div>
            <div class="row">
                <div class="col-lg-6"><i class="fa fa-map-marker"></i> 客户地址:{{$v->Adress}}</div>
                <div class="col-lg-6"><i class="fa fa-check"></i>审核状态:{{$v->Review}}</div>
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
                equipment += "<td><a href='"+href1+"' class='btn btn-sm btn-success'>充值 <i class='fa fa-rmb'></i></a> <a href='"+href2+"' class='btn btn-sm btn-warning'>赠送 <i class='fa fa-gift'></i></a></td>";
                equipment += "</tr>";
            });
            content.html(equipment);
        }, "json");
    }
});
</script>