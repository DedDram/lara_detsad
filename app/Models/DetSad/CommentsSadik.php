<?php

namespace App\Models\DetSad;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CommentsSadik extends Model
{
    public static function getAllComments()
    {
        $comments = DB::table('i1il4_comments_items')->select(DB::raw('COUNT(id) as total'))->first();
        return $comments->total;
    }
    public static function newComments(): ?object
    {
        return DB::table('lwa7r_comments_items as t1')
            ->select("t1.*", "t2.name as nameVuz" ,DB::raw("CONCAT('/', t3.id, '-', t3.alias, '/', t2.id, '-', t2.alias) as url"))
            ->join('i1il4_detsad_items as t2', 't2.id', '=', 't1.object_id')
            ->join('i1il4_detsad_categories as t3', 't3.id', '=', 't2.category_id')
            ->orderBy('t1.created', 'desc')
            ->limit(5)
            ->get();
    }
}
