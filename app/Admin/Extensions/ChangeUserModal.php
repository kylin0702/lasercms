<?php
namespace App\Admin\Extensions;

use Encore\Admin\Admin;
use Encore\Admin\Form\Builder;
use Encore\Admin\Form\Tools;
use Illuminate\Contracts\Support\Renderable;

class ChangeUserModal implements Renderable
{


    public function render()
    {

        return <<<EOT
<span >
<button type="button"  data-toggle='modal' data-target='#opModal' class="btn btn-primary">更改用户名</button>
</span>
<div class="modal fade" id="opModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" >
        <div class="modal-content" >
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">更改用户名</h4>
            </div>
            <div class="modal-body">
             <div class="form-inline">
                  <div class="form-group">
                    <label for="phone">新用户</label>
                    <input type="text" class="form-control" id="phone" placeholder="请输入手机号码">  <button type="button" class="btn btn-default btn-send">发送验证码</button>
                    <br/><br/>
                    <label for="code">验证码</label>
                    <input type="text"  class="form-control" id="code" placeholder="请输验证码">
                  </div>
                
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary btn-confirm"  id="btn-confirm"><b>确定</b></button>
            </div>
        </div>
    </div>
</div>
<script>

var code="";
$(".btn-send").on("click",function(){
    var phone=$("#phone").val();
    if(isPoneAvailable(phone)){
        $.post("/admin/clients/sms_change_username",{"phone":phone},function(data){
        if(data=="false"){
            swal("手机号码已注册");
        }
        else{
            code=data;
            $("#phone").attr("disabled","disabled");
            $(".btn-send").attr("disabled","disabled");
            swal("验证码已发送");
        }
    });
    }
    else{
        swal("输入的手机号码格式不对");
    }
});
$(".btn-confirm").on("click",function(){
    var mycode=$("#code").val();
    if(code==""){
         swal("请先发送验证码");
         return false;
    }
    else{
        if(mycode==code){
             var phone=$("#phone").val();
            $.post("/admin/clients/change_username",{"phone":phone},function(data) {
              if(data.result){
                   swal("用户名已更换,请使用重新登陆");
                   window.location.href="/admin/auth/logout";
              }
            });
        }
        else{
              swal("请输入正确的验证码");
        }
    }
    
    
    
});
  function isPoneAvailable(str) {
            var myreg=/^[1][3,4,5,7,8,9][0-9]{9}$/;
            if (!myreg.test(str)) {
                return false;
            } else {
                return true;
            }
        }

</script>
EOT;
    }
}