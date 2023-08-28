<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;
    protected $table = 'material';
    public $primaryKey = 'ID_Material';
    protected $fillable = [
        'Name_Material'
    ];

    public function detailProductMaterial(){
        return $this->hasMany(DetailProductMaterial::class,'ID_Material');
    }
}
