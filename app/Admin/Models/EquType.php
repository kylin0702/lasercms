<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class EquType extends Model
{
    protected $table = 'EquType';
    protected  $primaryKey="ID";

    public function Equipment()
    {
        return $this->hasOne(Equipment::class);
    }
}
