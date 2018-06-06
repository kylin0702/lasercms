<?php

namespace App\Admin\Models;

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{

    protected $table = 'Client';
    protected $primaryKey="ID";
    public  $timestamps=false;


    public function  hasOneArea(){
        return $this->hasOne(Area::class,"ID","AreaID");
    }

    public function  hasOneAuditor(){
        return $this->hasOne("Encore\Admin\Auth\Database\Administrator" ,"id","EntryPer");
    }

    public function hasManyRecharge()
    {
        return $this->hasMany(Recharge::class, 'ClientID', 'ID');
    }

    public function hasOneUser()
    {
        return $this->hasOne(Administrator::class, 'username', 'username');
    }

    public function hasOneEngineer()
    {
        return $this->hasOne(Administrator::class, 'username', 'engineer');
    }

    public function hasManyEquipment()
    {
        return $this->hasMany(Equipment::class,"ClientID","ID");
    }
}
