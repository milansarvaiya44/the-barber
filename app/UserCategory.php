<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCategory extends Model
{
    protected $table = 'usercategory';

    public function user()
    {
    return $this->belongsTo('App\User');
    }
}

