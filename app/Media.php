<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    //
    protected $table = 'media';
    public $primaryKey = 'id';
    public $timestamps = true;
    protected $appends = ['imagePath'];

    public function service()
    {
        return $this->hasMany('App\Service');
    }
    public function getImagePathAttribute()
    {
        return url('storage/videos/media') . '/';
    }
}
