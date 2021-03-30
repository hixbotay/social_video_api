<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Enums\VideoStatusEnum;

class Video extends Model
{
    use HasFactory;
    protected $table = 'api_videos';
    public $timestamps = true;
 
    protected $casts = [
        
    ];
 
    protected $fillable = [
	'user_id','title','description', 'path','thumbnail_path','status','view'
    ];
 
    public function user(){
        return $this->belongsTo(User::class,'user_id','ID');
    }
	
	public function scopeNewFeedTab($query,$user){
        $query = $query->isActive()->select(['api_videos.*'])
        ->leftJoin('api_friend_relations as r', 'api_videos.user_id', '=', 'r.to_user_id')
        ->where(function($query) use ($user) {
            $query->orWhere('api_videos.user_id', $user->ID)
                ->orWhere(function($query) use ($user) {
                    $query->where('r.from_user_id',$user->ID)
                        ->where('r.is_follow',true);
            });
        });		
        return $query;
		
	}
	public function scopeIsActive($query){
		return $query->where('status' ,'=', VideoStatusEnum::PUBLIC['value']);
	}
	public function scopeSearch($query, $key){
        return $query->where(function($query) use ($key) {
                $query->orWhere('title','like',"%{$key}%")
				->orWhere('description','like',"%{$key}%");
            })->isActive()->orderBy('title');
    }
}