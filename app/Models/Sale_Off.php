<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale_Off extends Model
{
    use HasFactory;
    protected $table = "Sale_Off";
    public $timestamps = false;

    public $primaryKey = 'ID_SO';
    protected $fillable=[
        'Name_SO',
        'Discount_Percent_SO',
        'Start_Date_SO',
        'End_Date_SO',
    ];
}
