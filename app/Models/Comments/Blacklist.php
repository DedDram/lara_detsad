<?php

namespace App\Models\Comments;

use Illuminate\Database\Eloquent\Model;

class Blacklist extends Model
{
    protected $table = 'i1il4_comments_items';
    protected $primaryKey = 'id';
    protected $fillable = ['created', 'ip'];


    public static function getBlacklist(string $ip)
    {
        return self::where('ip', $ip)
            ->where('created', '>', now()->subDay())
            ->exists();
    }

}
