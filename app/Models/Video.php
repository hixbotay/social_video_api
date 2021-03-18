<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
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
}