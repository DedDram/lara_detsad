<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comments extends Model
{
    protected $table = 'i1il4_comments_items';
    protected $primaryKey = 'id';
    protected $fillable = ['object_group', 'object_id', 'created', 'ip', 'user_id', 'rate', 'country', 'status', 'username', 'email', 'isgood', 'ispoor', 'description', 'images'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public static function totalCommentsCount()
    {
        return self::count();
    }
}
