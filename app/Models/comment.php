<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'ID_Product',
        'ID_User',
        'content',
        'reply_to'
    ];

    public function parentComment()
    {
        return $this->belongsTo(comment::class,'reply_to');
    }
    public function replies()
    {
        return $this->hasMany(comment::class, 'reply_to' );
    }
    public function user()
    {
        return $this->belongsTo(User::class,'ID_User');
    }
}
