<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetsadImage extends Model
{
    protected $table = 'i1il4_detsad_images';
    protected $primaryKey = 'id';
    public $timestamps = false; // Если в таблице нет полей created_at и updated_at

    protected $fillable = [
        'item_id',
        'thumb',
        'original_name',
        'alt',
        'title',
        'verified',
    ];

    public function sadik(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
