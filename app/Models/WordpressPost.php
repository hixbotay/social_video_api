<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class WordpressPost extends Model
{
    use HasFactory;
	protected $table = 'wp_posts';
    public $timestamps = false;

    protected $casts = [
    ];

    protected $fillable = [
        'ID',
        'post_title','post_content','post_author'
    ];

    protected $hidden = [
        // 'path',
    ];

    public static function getVideo($offset = 0){
        $posts = DB::select("SELECT post.ID as id, post.post_title as title, post.post_content as description, post.post_date as created_at,post.post_author FROM wp_posts as post WHERE post_type LIKE ? AND post_status LIKE ?  ORDER BY post.ID DESC LIMIT {$offset},5",['post','publish']);
		
		$post_ids = array_map ( function ($a) {
			return $a->id;
		}, $posts );
		$user_ids = array_map ( function ($a) {
			return $a->post_author;
		}, $posts );
		
		$metas = DB::select("SELECT post_id,meta_key,meta_value
		from wp_postmeta 		   
		WHERE 
			post_id IN (".implode(',',$post_ids).")
			");
		$users = User::whereIn('ID',$user_ids)->get()->toArray();
		
		foreach($posts as &$post){
			$post->path = self::getKeyOfPost($metas,$post->id,'trailer_video');
			$post->thumbnail_path = self::getKeyOfPost($metas,$post->id,'_video_thumbnail');
			$post->number_like = self::getKeyOfPost($metas,$post->id,'_video_network_likes');
			$post->number_comment = self::getKeyOfPost($metas,$post->id,'_video_network_comments');
			$post->view = self::getKeyOfPost($metas,$post->id,'_video_network_views');
			$post->user = array_filter($users,function ($a) use ($post){
				return $post->post_author==$a['ID'];
			})[0];
		}				
		
		return $posts;
		
    }
	
	static function getKeyOfPost($metas,$post_id,$key){
		foreach($metas as $meta){
			if($meta->post_id==$post_id && $meta->meta_key==$key){
				return $meta->meta_value;
			}
		}
	return '';
	}
	
	public function user(){
        return $this->belongsTo(User::class,'post_author','ID');
    }
	public function meta(){
        return $this->belongsTo(User::class,'ID','post_id');
    }
}
