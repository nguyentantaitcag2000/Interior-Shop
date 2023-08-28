<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table = 'supplier';
    public $primaryKey = 'ID_S';
    protected $fillable = [
        'Name_S',
        'Address_S',
        'Phone_S',
        'Email_S',
        'Website_S'
        
    ];
    public function product()
    {
        return $this->hasMany(Product::class,'ID_S');
    }
}
