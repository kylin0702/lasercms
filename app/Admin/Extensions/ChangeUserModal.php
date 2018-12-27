<?php
namespace App\Admin\Extensions;

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
                    <input type="text" class="form-control" id="phone" placeholder="请输入手机号码">
                  </div>
                  <button type="button" class="btn btn-default">发送验证码</button>
                </div>
            </div>
             <div class="form-inline">
                  <div class="form-group">
                    <label for="phone">验证码</label>
                    <input type="text" class="form-control" id="code" placeholder="请输验证码">
                  </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" disabled="disabled" id="btn-confirm"><b>确定</b></button>
            </div>
        </div>
    </div>
</div>
EOT;
    }
}