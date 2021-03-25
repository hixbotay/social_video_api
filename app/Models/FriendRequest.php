<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model
{
    use HasFactory;

	protected $table = 'api_friend_requests';
    
    protected $fillable = [
        'from_user_id', 'to_user_id','is_friend', 'is_follow'
    ];
	protected $hidden = ['id'];

	public function from_user(){
        return $this->belongsTo(User::class,'from_user_id','ID');
    }
	public function to_user(){
        return $this->belongsTo(User::class,'to_user_id','ID');
    }
}
