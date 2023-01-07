<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teams extends Model
{
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo('App\Models\Categories', 'category_id');
    }
}
