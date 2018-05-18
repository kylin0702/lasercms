<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;

class Area extends Model
{
    use ModelTree, AdminBuilder;
    protected $table = 'Area';
    protected $primaryKey="ID";
    public  $timestamps=false;
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setParentColumn('Superior');
        $this->setOrderColumn('Order');
        $this->setTitleColumn('AreaName');
    }
}
