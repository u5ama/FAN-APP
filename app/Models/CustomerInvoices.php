<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerInvoices extends Model
{
    protected $table = 'customer_invoices';
    protected $guarded = [];

    public function player()
    {
        return $this->hasOne('App\Models\User', 'id','player_id');
    }

    public function gifts()
    {
        return $this->hasOne('App\Models\Gifts','id', 'gift_id');
    }

    public function fan()
    {
        return $this->hasOne('App\Models\User', 'id','user_id');
    }
}
