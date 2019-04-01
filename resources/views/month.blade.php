
<div class="row"><div class="col-md-12"><div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">
                    年份: <span>
                            <select class="year-selecter">
                            <option value="2017">2017年</option>
                            <option value="2018" >2018年</option>
                            <option value="2019" selected>2019年</option>
                            </select>
                            </span>
                    月份: <span>
                            <select class="month-selecter">
                            <option value="1">一月</option>
                            <option value="2">二月</option>
                            <option value="3">三月</option>
                            <option value="4">四月</option>
                            <option value="5">五月</option>
                            <option value="6">六月</option>
                            <option value="7">七月</option>
                            <option value="8">八月</option>
                            <option value="9">九月</option>
                            <option value="10">十月</option>
                            <option value="11">十一月</option>
                            <option value="12">十二月</option>
                            </select>
                            </span>
                    客户名称:
                    <select class="client-selecter">
                        <option value="">全部</option>
                        @foreach($client as $value)
                        <option value="{{$value->ID}}">{{$value->ClientName}}</option>
                        @endforeach
                    </select>
                        <input type="radio" name="isbuy" value="0" checked />租赁
                        <input type="radio" name="isbuy" value="1" />测试
                    </span>
                    <button type="button" class="btn btn-success btn-query"><i class="fa fa-search"></i> 查询</button>
                </h3>
                <div class="pull-right">
                    <button type="button" class="btn btn-info btn-export pull-right">导出Excel</button>
                </div>
            </div>
            <div class="box-body">
                @if(count($output)>0)
                <table class="table table-bordered table-responsive table-condensed">
                    <thead>
                        <tr>
                            <th>客户编码</th><th>财产编号</th><th>客户名称</th><th>厅号</th><th>机型</th><th>光源编号</th><th>上月余额小时</th><th>本月充值小时数(含赠送)</th><th>本月使用小时数</th><th>剩余小时数</th><th>本年累计小时数</th><th>本月应扣除小时数</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($output as $v)
                    <tr>
                        <td>{{$v['客户编码']}}</td><td>{{$v['财产编号']}}</td><td>{{$v['客户名称']}}</td><td>{{$v['厅号']}}</td><td>{{$v['机型']}}</td><td>{{$v['光源编号']}}</td><td>{{$v['上月余额小时']}}</td><td>{{$v['本月充值小时数(含赠送)']}}</td><td>{{$v['本月使用小时数']}}</td><td>{{$v['剩余小时数']}}</td><td>{{$v['本年累计小时数']}}</td><td>{{$v['本月应扣除小时数']}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div></div>
<!-- loading -->
<div class="modal fade" id="loading" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-body">
       <i class="fa fa-spin fa-spinner"></i> 数据生成中,请稍候。。。
      </div>
    </div>
  </div>
</div>
<script>
    function getQueryString(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
        var reg_rewrite = new RegExp("(^|/)" + name + "/([^/]*)(/|$)", "i");
        var r = window.location.search.substr(1).match(reg);
        var q = window.location.pathname.substr(1).match(reg_rewrite);
        if(r != null){
            return unescape(r[2]);
        }else if(q != null){
            return unescape(q[2]);
        }else{
            return null;
        }
    }
    $(function () {
        //加载客户选择
        $(".client-selecter").select2();
        var month=getQueryString("month");
        var year=getQueryString("year");
        var clientid=getQueryString("client");
        var isbuy=getQueryString("isbuy");
        if(month!=null){
            $(".year-selecter option[value="+year+"]").attr("selected", "selected");
            $(".month-selecter option[value="+month+"]").attr("selected", "selected");
            $("input[name='isbuy'][value='" + isbuy + "']").attr("checked", "checked");

        }
    });
    $(".btn-query").on('click',function(){
        var year=$(".year-selecter").val();
        var month=$(".month-selecter").val();
        var clientid=$(".client-selecter").val();
        var isbuy=$("input[name='isbuy']:checked").val();
        window.location.href="/admin/month?year="+year+"&month="+month+"&client="+clientid+"&isbuy="+isbuy;
    });
    $(".btn-export").on('click',function(){
        var year=$(".year-selecter").val();
        var month=$(".month-selecter").val();
       window.open("/admin/month/month_excel?year="+year+"&month="+month);
    });
</script>
