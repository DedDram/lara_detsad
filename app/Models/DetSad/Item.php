<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    protected $table = 'i1il4_detsad_items';
    protected $primaryKey = 'id';
    protected $fillable = [
        'category_id',
        'section_id',
        'alias',
        'name',
        'title',
        'keywords',
        'description',
        'header',
        'affiliation',
        'text',
        'preview_src',
        'preview_alt',
        'preview_title',
        'preview_border',
        'rating_plus',
        'rating_minus',
        'rating_sum',
        'okrug',
        'rate',
        'vote',
        'average',
        'comments',
        'nearby',
        'inn',
        'sid'
    ];

    public function addresses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Address::class, 'item_id');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function section(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

}
