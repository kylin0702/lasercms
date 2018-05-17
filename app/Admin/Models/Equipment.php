<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $table = 'Equipment';
    protected $primaryKey="ID";

    #关联EquType模型
    public function EquType()
    {
        return $this->belongsTo(EquType::class,'EquTypeID');
    }

    #关联EquType模型
    public function Client()
    {
        return $this->belongsTo(Client::class,'ClientID');
    }

    #关联EquStatus模型
    public function EquStatus()
    {
        return $this->belongsTo(Client::class,'sNU');
    }
}
