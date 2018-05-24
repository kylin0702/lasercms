<?php

namespace App\admin\Models;

use Illuminate\Database\Eloquent\Model;

class Recharge extends Model
{
    protected $table = 'Recharge';
    protected $primaryKey="ID";
    public $timestamps=false;

    public $casts=[
            "Amount"=>"integer",
            "UpdateTime"=>"DateTime"
    ];

    //关联Client表
    public function hasOneClient()
    {
        return $this->hasOne(Client::class,"ID","ClientID");
    }

    //关联Equipment表
    public function hasOneEqu()
    {
        return $this->hasOne(Equipment::class,"ID","EquID");
    }
}
