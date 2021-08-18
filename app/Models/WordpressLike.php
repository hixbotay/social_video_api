<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class WordpressLike extends Model
{
    use HasFactory;
	protected $table = 'wp_wti_like_post';
    public $timestamps = false;
	protected $primaryKey = 'id';

    protected $casts = [
    ];

    protected $fillable = [
        'id',
        'post_id','value','user_id','date_time'
    ];

    protected $hidden = [
        // 'path',
    ];

	public function user(){
        return $this->belongsTo(User::class,'user_id','ID');
    }

	
}
