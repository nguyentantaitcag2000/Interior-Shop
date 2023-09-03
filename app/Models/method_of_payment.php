<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class method_of_payment extends Model
{
    use HasFactory;
    protected $primaryKey = 'ID_MOP';
    public $table = 'methodofpayment';
    public $timestamps = false;
    protected $fillable = [
        'Name_MOP'
    ];

}
