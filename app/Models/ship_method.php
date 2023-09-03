<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ship_method extends Model
{
    use HasFactory;
    protected $primaryKey = 'ID_SM';
    public $table = 'ship_method';
    public $timestamps = false;
    protected $fillable = [
        'Name_SM'
    ];
}
