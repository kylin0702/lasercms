<?php

namespace App\admin\Models;

use Illuminate\Database\Eloquent\Model;

class DateBalance extends Model
{
    protected $table = 'DateBalance';
    protected $primaryKey="ID";
    protected $fillable=["EquID","FirstTime","LastTime","RechargeTime","CostTime","BalanceDate"];
    public $timestamps=false;
    #关联EquType模型
    public function hasOneEqu()
    {
        return $this->hasOne(Equipment::class,'ID',"EquID");
    }

}
