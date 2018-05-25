<?php

namespace App\admin\Models;

use Illuminate\Database\Eloquent\Model;

class Abnorma extends Model
{
    protected $table = 'Abnorma';
    protected  $primaryKey="ID";
    public $timestamps=false;

    public function hasOneEquipment(){
        return $this->hasOne(Equipment::class,"ID","EquID");
    }

    public function hasOneClient(){
        return $this->hasOne(Client::class,"ID","ClientID");
    }

}
