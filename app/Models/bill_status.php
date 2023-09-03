<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class bill_status extends Model
{
    use HasFactory;
    protected $primaryKey = 'ID_BS';
    public $table = 'bill_status';
    public $timestamps = false;
    protected $fillable = [
        'Name_BS',
        
    ];
}
