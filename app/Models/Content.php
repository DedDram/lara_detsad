<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Content extends Model
{
    protected $table = 'i1il4_content';
    protected $primaryKey = 'id';

    protected $fillable =
        [
            'title',
            'alias',
            'introtext',
            'fulltext',
            'catid',
            'created',
            'modified',
            'metakey',
            'metadesc',
            'rate',
            'vote',
            'average',
            'comments',
        ];

    public static function getUrl(int $objectId): ?string
    {
        $result = DB::table('i1il4_content as t1')
            ->select(DB::raw("CONCAT('/', t2.alias, '/', t1.id, '-', t1.alias) as url"))
            ->join('i1il4_categories as t2', 't2.id', '=', 't1.catid')
            ->where('t1.id', $objectId)
            ->first();

        return $result ? $result->url : '';
    }

    public function getContent(int $id)
    {
        return DB::table('i1il4_content as t1')
            ->select("t1.*", "t2.id as cat_id", "t2.alias as cat_alias")
            ->join('i1il4_categories as t2', 't2.id', '=', 't1.catid')
            ->where('t1.id', $id)
            ->first();
    }
}
