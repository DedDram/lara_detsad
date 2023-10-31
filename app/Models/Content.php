<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Content extends Model
{
    public static function getUrl(int $objectId): ?string
    {
        $result = DB::table('i1il4_content as t1')
            ->select(DB::raw("CONCAT('/', t2.alias, '/', t1.id, '-', t1.alias) as url"))
            ->join('i1il4_categories as t2', 't2.id', '=', 't1.catid')
            ->where('t1.id', $objectId)
            ->first();

        return $result ? $result->url : '';
    }
}
