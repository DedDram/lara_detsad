<?php

namespace App\Models\Exchange_Job;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExchangeJobTeachers extends Model
{
    protected $table = 'i1il4_ads_teachers';
    protected $primaryKey = 'id';

    protected $fillable =
        [
            'name',
        ];

    public static function getTeachersSearch(): array
    {
        $objects = DB::table('i1il4_ads_teachers')
            ->select('id', 'name AS title')
            ->orderBy('name', 'asc')
            ->get()
            ->toArray();
        $teachers = array_map(function ($item) {
            return (array) $item;
        }, $objects);

        array_unshift($teachers, array('title' => '- Специальность -', 'id' => 0));
        return $teachers;
    }
}
