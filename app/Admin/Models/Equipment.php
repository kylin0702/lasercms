<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'Equipment';
    protected $primaryKey="ID";
    public $timestamps=false;
    protected $fillable = ['EquNum',"OldEquNum"];
    protected $casts=[
        "RemainTime"=>"integer",
        "GiftTime"=>"integer"
    ];


    #关联EquType模型
    public function hasOneEquType()
    {
        return $this->hasOne(EquType::class,'ID',"EquTypeID");
    }

    #关联EquType模型
    public function hasOneClient()
    {
        return $this->hasOne(Client::class,'ID',"ClientID");
    }

    #关联upanddown模型
    public function upanddown()
    {
        return $this->hasMany(UpAndDown::class,'EquNum',"EquNum");
    }


}
