<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class EquStatus extends Model
{
    protected $table = 'EquStatus';
    public $timestamps=false;
    protected $primaryKey="ID";
    protected $fillable = ['sNU'];

}
