<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill_Status_History extends Model
{
    use HasFactory;
    protected $primaryKey = 'ID_BILL';
    public $table = 'bill_status_history';
    public $timestamps = false;
    protected $fillable = [
        'ID_User',
        'ID_BS',
        'ID_BILL',
    ];
    function user()
    {
        return $this->belongsTo(User::class, 'ID_User');
    }
    function bill_status()
    {
        return $this->belongsTo(bill_status::class, 'ID_BS');
    }
}
