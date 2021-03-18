<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
 
class UserMeta extends Model
{
    use HasFactory;
    protected $table = 'user_meta';
    public $timestamps = false;
 
    protected $fillable = [
        'user_id','photo_path','is_verify','verify_photo','fb_id'
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','ID');
    }
}