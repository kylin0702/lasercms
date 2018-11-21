<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;

class EquStatusTemp extends Model
{
    protected $table = 'EquStatus0312';
    public $timestamps=false;
    protected $primaryKey="ID";
    protected $fillable = ['sNU'];

}