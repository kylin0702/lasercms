<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class EquStatusTemp extends Model
{
    protected $table = 'EquStatus0913';
    public $timestamps=false;
    protected $primaryKey="ID";
    protected $fillable = ['sNU'];

}