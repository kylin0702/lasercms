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
                <div class="col-lg-2"><i class="fa fa-user"></i> 法人名称:{{$v->Owner}}</div>
                <div class="col-lg-2"><i class="fa fa-mobile"></i> 联系方式:{{$v->Phone}}</div>
                <div class="col-lg-2"><i class="fa fa-sitemap"></i> 所属区域:{{$v->hasOneArea->AreaName}}</div>
                <div class="col-lg-2"><i class="fa fa-calendar-check-o"></i>注册时间:{{date("Y-m-d",strtotime($v->UpdateTime))}}</div>
                <div class="col-lg-2"><i class="fa fa-check"></i>审核状态:{{$v->Review}}</div>
            </div>
            <div class="row">
                <div class="col-lg-4"><i class="fa fa-map-marker"></i> 客户地址:{{$v->Adress}}</div>
                <div class="col-lg-2"></div>
                <div class="col-lg-2">
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
                equipment += "<td>" + e.has_one_equ_type.Name + "</td>"
                equipment += "<td>" + remainTime + "</td>"
                equipment += "<td>" + status + "</td>";
                equipment += "<td>" + e.ReviewTime + "</td>";
                equipment += "<td><a href='javascript:void(0);' class='btn btn-sm btn-info btn-recharge'>查看充值记录</a>  ";
                equipment += "<a href='javascript:void(0);' class='btn btn-sm btn-success btn-report'>导出时长报表</a></td>";
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
</script>