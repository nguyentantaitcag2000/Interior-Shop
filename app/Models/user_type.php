<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_type extends Model
{
    use HasFactory;
    protected $primaryKey = 'ID_UT';
    public $table = 'user_type';
    public $timestamps = false;
    protected $fillable = [
        'Name_UT',
        
    ];
}
