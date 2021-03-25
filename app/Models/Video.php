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
	
	public function newFeedTab($user){
		$this->isActive();					
		return $this;
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