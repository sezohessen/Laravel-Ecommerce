<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
class Order_product extends Model
{
    protected $guarded = [];
    public function Order()
    {
        return $this->belongsTo(Order::class);
    }
}
