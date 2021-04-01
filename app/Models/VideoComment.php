<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoComment extends Model
{
    use HasFactory;
	protected $table = 'api_video_comments';
    public $timestamps = true;

    protected $casts = [
    ];

    protected $fillable = [
        'video_id',
        'user_id',
		'comment',
		'parent_id'
    ];

    protected $hidden = [
        // 'path',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id','ID');
    }
	
	
	
}
