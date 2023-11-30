<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ContentCategory extends Model
{
    protected $table = 'i1il4_categories';
    protected $primaryKey = 'id';

    protected $fillable =
        [
            'parent_id',
            'title',
            'alias',
            'description',
            'metadesc',
            'metakey',
        ];


    public function contents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Content::class, 'catid', 'id');
    }

}
