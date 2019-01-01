<?php
namespace App\Admin\Extensions;

use Encore\Admin\Grid\Tools\AbstractTool;

class MonthExporter extends AbstractTool
{
    public function render()
    {

        return <<<EOT
<span class="col-md-offset-7">
<select class="year-selecter">
<option value="2017">2017年</option>
<option value="2018">2018年</option>
<option value="2019" selected>2019年</option>
</select>
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
<button type="button" class="btn btn-adn">生成月报表</button>
</span>
<script>
$(function(){
 var current=(new Date()).getMonth();
$('.month-selecter').val(current+1);
})

$(".btn-adn").on("click",function(){
    var y=$('.year-selecter').val();
    var m=$('.month-selecter').val();
    window.open("balances/month_excel?year="+y+"&month="+m);
});
</script>
EOT;
    }
}