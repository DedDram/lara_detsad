<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;

class DetsadImage extends Model
{
    protected $table = 'i1il4_detsad_images';
    protected $primaryKey = 'id';
    public $timestamps = false; // Если в таблице нет полей created_at и updated_at

    protected $fillable = [
        'item_id',
        'thumb',
        'original',
        'alt',
        'title',
        'verified',
    ];
}
