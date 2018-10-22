<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class UpAndDown extends Model
{
    protected $table = 'UpAndDown';
    protected $primaryKey='ID';
    #关联Equipemnt模型
    public function hasOneEqu()
    {
        return $this->belongsTo(Equipment::class,'EquNum',"EquNum");
    }
}