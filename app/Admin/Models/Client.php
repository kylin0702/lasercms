<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    protected $table = 'Client';
    protected $primaryKey="ID";
    public  $timestamps=false;


    public function  area(){
        return $this->belongsTo(Area::class,"AreaID","ID");
    }

    public function  auditor(){
        return $this->belongsTo("Encore\Admin\Auth\Database\Administrator" ,"EntryPer");
    }

    public function hasManyRecharge()
    {
        return $this->hasMany(Recharge::class, 'ClientID', 'ID');
    }
}
