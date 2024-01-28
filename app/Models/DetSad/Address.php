<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $table = 'i1il4_detsad_address';
    protected $primaryKey = 'id';
    protected $fillable =
        [
            'status',
            'item_id',
            'geo_code',
            'geo_lat',
            'geo_long',
            'country_name',
            'region',
            'locality',
            'street_address',
            'street_alias',
            'district',
            'metro',
            'nearby'
        ];
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
