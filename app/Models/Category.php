<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = "category";
    public $timestamps = false;

    public $primaryKey = 'ID_Category';
    protected $fillable=[
        'Name_Category',
        'Icon',
    ];
    public function product()
    {
        return $this->hasMany(Product::class, 'ID_Category');
    }
}
