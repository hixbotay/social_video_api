<?php
 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    protected $table = 'api_video_favorite';
    public $timestamps = true;
 
    protected $casts = [
        'list' => 'array'
    ]; 
 
    protected $fillable = [
	'id','user_id','name','list','ordering'
    ];

    protected $appends = [
    ];
 
    public function user(){
        return $this->belongsTo(User::class,'user_id','ID');
    }
	
	public function scopeUser($query, $user_id){
        return $query->where('user_id',$user_id);
    }
	
	public function updateRead(){
		return $this->update(['is_read' => 1 ]);
	}

}