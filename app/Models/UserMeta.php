<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
 
class UserMeta extends Model
{
    use HasFactory;
    protected $table = 'api_user_metas';
    public $timestamps = false;
    protected $primaryKey = 'user_id';
 
    protected $fillable = [
        'user_id', 'photo_path', 'is_verify', 'verify_photo', 'fb_id', 'number_friend', 'birthday','number_follow', 'number_follow_me', 'device_token'
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','ID');
    }
}