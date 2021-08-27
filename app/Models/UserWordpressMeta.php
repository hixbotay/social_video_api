<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
 
class UserWordpressMeta extends Model
{
    use HasFactory;
    protected $table = 'wp_usermeta';
    public $timestamps = false;
    protected $primaryKey = 'umeta_id';
 
    protected $fillable = [
        'umeta_id', 'user_id', 'meta_key', 'meta_value'
    ];
 
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','ID');
    }
}