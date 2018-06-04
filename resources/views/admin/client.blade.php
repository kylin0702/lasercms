@foreach ($clients as $v)
    <div class="box box-default collapsed-box">
        <div class="box-header with-border">
            <h3 class="box-title" data-widget="collapse">{{$v->ClientName}}</h3>
        </div><!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-lg-3">法人名称:{{$v->Owner}}</div>
                <div class="col-lg-3">联系方式:{{$v->Phone}}</div>
                <div class="col-lg-3">所属区域:{{$v->hasOneArea->AreaName}}</div>
                <div class="col-lg-3">注册时间:{{date("Y-m-d",strtotime($v->UpdateTime))}}</div>
            </div>
            <div class="row">
                <div class="col-lg-6">客户地址:{{$v->Adress}}</div>
                <div class="col-lg-6">审核状态:{{$v->Review}}</div>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /.box -->
@endforeach
