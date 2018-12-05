
    <div class="row"><div class="col-md-12"><div class="box box-info">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        开始时间: <input class="startdate">
                        结束时间: <input class="enddate"> &nbsp;
                        <button type="button" class="btn btn-success btn-query"><i class="fa fa-search"></i> 查询</button>
                    </h3>
                    <div class="pull-right">
                        @if(count($balances)>0) <b style="font-size: large">{{$date1}}至{{$date2}}使用时长合计: <b class="text-red">{{$total}}</b> 小时</b> @endif
                    </div>
                </div>
                    <div class="box-body">
                        @if(count($balances)>0)
                        <table class="table table-bordered table-responsive table-condensed">
                            <thead><tr><th>日期</th><th>起始剩余时长</th><th>结束剩余时长</th><th>充值时长</th><th>使用时长</th></tr></thead>
                            <tbody>
                            @foreach($balances as $v)
                            <tr><td>{{$v->BalanceDate}}</td><td>{{$v->FirstTime}}</td><td>{{$v->LastTime}}</td><td>{{$v->RechargeTime}}</td><td>{{$v->CostTime}}</td></tr>
                            @endforeach

                            </tbody>
                        </table>

                    </div>
                    <div class="box-footer">
                        <div class="col-md-12">
                            <div class="btn-group pull-right">
                                <button type="button" class="btn btn-info btn-export pull-right">导出</button>
                            </div>
                        </div>
                    </div>

            </div>
            @endif
        </div></div>
    <script>
        $(function () {
            $('.startdate').datetimepicker({showClose:true,format:"YYYY-MM-DD",locale:"zh-CN",widgetPositioning:{horizontal: 'right',vertical:'bottom'}});
            $('.enddate').datetimepicker({showClose:true,format:"YYYY-MM-DD",locale:"zh-CN",widgetPositioning:{horizontal: 'right',vertical:'bottom'}});
        });
        $(".btn-query").on('click',function(){
            var date1= $('.startdate').val();
            var date2= $('.enddate').val();
            if(date1==""||date2==""){
                alert("请输入开始时间和结束时间");
                return false;
            }
            window.location.href="/admin/durt/query/{{$id}}?date1="+date1+"&date2="+date2;
        });
        $(".btn-export").on('click',function(){
            window.open("/admin/durt/exportExcel?id={{$id}}&date1={{$date1}}&date2={{$date2}}");
        });
    </script>