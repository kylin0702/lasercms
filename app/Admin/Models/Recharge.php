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

    public function client()
    {
        return $this->belongsTo(Client::class,"ClientID");
    }

    public function equ()
    {
        return $this->belongsTo(Equipment::class,"EquID");
    }
}
