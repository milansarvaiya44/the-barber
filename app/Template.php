<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $table = 'template';
    public $primaryKey = 'id';
    public $timestamps = true;
}
