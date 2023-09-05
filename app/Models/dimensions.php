<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dimensions extends Model
{
    use HasFactory;
    protected $primaryKey = 'ID_D';
    protected $fillable = [
        'Name_D',
        'ID_Product',
        
    ];
}
