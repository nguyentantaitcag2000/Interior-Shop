<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class import_history extends Model
{
    use HasFactory;
    protected $table = 'import_history';
    public $timestamps = false;
    public $primaryKey = 'ID_IH';
    protected $fillable = [
        'TotalMoney_IH'
    ];

}
