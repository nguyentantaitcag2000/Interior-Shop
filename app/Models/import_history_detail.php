<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class import_history_detail extends Model
{
    use HasFactory;

    protected $fillable = [
        'Price_IDH',
        'Amount_IDH',
        'ID_Color',
        'ID_Material',
        'ID_D',
        'ID_Product',
        'ID_IH',
    ];
}
