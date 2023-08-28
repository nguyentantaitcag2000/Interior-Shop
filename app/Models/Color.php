<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;
    protected $table = 'color';
    public $primaryKey = 'ID_Color';
    protected $fillable = [
        'Name_Color'
    ];
    public function detailProductColor()
    {
        return $this->hasMany(DetailProductColor::class,"ID_Color");
    }
}
