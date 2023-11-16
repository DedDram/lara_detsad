<?php

namespace App\Models\Exchange;

use Illuminate\Database\Eloquent\Model;

class ExchangeItems extends Model
{
    protected $table = 'i1il4_ads_items';
    protected $primaryKey = 'id';
    const CREATED_AT = 'created';
    const UPDATED_AT = 'modified';
    protected $fillable =
        [
            'city_id',
            'metro_id',
            'teacher_id',
            'type',
            'ip',
            'created',
            'modified',
            'status',
            'hash',
            'fullname',
            'phone',
            'email',
            'photo',
            'text',
            'teach',
        ];

    public function city(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExchangeCity::class, 'city_id');
    }
    public function metro(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExchangeMetro::class, 'metro_id');
    }
    public function teachers(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ExchangeTeachers::class, 'teacher_id');
    }
}
