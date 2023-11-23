<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User_UStatus extends Model
{
    use HasFactory;

    protected $table = 'User_UStatus';
    public $primaryKey = 'ID_UStatus';
    public $timestamps = false;
    
    protected $fillable = [
        'Name_UStatus',
    ];
}
