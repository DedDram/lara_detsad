<?php

namespace App\Models\Comments;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    protected $table = 'i1il4_comments_blacklist';
    protected $primaryKey = 'id';
    protected $fillable = ['created', 'ip'];


    public static function getBlacklist(string $ip): bool
    {
        $count = self::where('ip', $ip)
            ->where('created', '>', now()->subDay())
            ->count();
        return $count > 0;
    }

}
