<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function pictures()
    {
        return $this->hasMany(product_picture::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public static function active()
    {
        return self::where('active', 1);
    }
}
