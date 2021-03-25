<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendRelation extends Model
{
    use HasFactory;

	protected $table = 'api_friend_relations';
    protected $hidden = ['id'];
    

	protected $fillable = [
        'from_user_id', 'to_user_id','is_friend', 'is_follow'
    ];
}
