<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['name', 'category_id', 'price', 'stock'];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public $casts = [
        'price' => 'integer',
        'stock' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
