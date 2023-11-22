<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review_Rating extends Model
{
    use HasFactory;
    protected $table = "Review_Rating";
    public $timestamps = false;
    protected $fillable=[
        'Content_RR',
        'ID_Product',
        'ID_User',
        'ID_Rate',
    ];

    function Rate()
    {
        return $this->belongsTo(Rate::class, 'ID_Rate');
    }
    function User()
    {
        return $this->belongsTo(User::class, 'ID_User');
    }
}
