<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{

    protected $table = 'Client';
    protected $primaryKey="ID";
    public  $timestamps=false;


    public function  area(){
        return $this->belongsTo(Area::class,"AreaID","ID");
    }

    public function  hasOneAuditor(){
        return $this->hasOne("Encore\Admin\Auth\Database\Administrator" ,"id","EntryPer");
    }

    public function hasManyRecharge()
    {
        return $this->hasMany(Recharge::class, 'ClientID', 'ID');
    }
}
