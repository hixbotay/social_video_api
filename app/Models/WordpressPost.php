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
	protected $primaryKey = 'ID';

    protected $casts = [
    ];

    protected $fillable = [
        'ID',
        'post_title','post_content','post_author'
    ];

    protected $hidden = [
        // 'path',
    ];

	public static function updateMeta($post_id,$meta_key,$value){
		return DB::table('wp_postmeta')
		->updateOrInsert(
			[
				'post_id' => $post_id,
				'meta_key' => $meta_key,
			],
			['meta_value' => $value]
		);
	}

	public static function getMeta($post_id,$meta_key=''){
		$query = DB::table('wp_postmeta')
		->where('post_id', $post_id);
		if($meta_key){
			$query->where('meta_key',$meta_key);
		}
		$data = $query->get()->toArray();
		if($meta_key){
			if($data)
				return $data[0]->meta_value;
			else
				return 0;
		}else{
			$result = [];
			foreach($data as $arr){
				$result[$arr->meta_key] = $arr->meta_value;
			}
			return $result;
		}
	}

	public function scopeIsActive($query){
		return $query->where('post_status' ,'=', 'publish');
	}


	public static function fetchMetaKey($posts){
		$post_ids = array_map ( function ($a) {
			return $a['ID'];
		}, $posts );
		$user_ids = array_map ( function ($a) {
			return $a['post_author'];
		}, $posts );
		
		$metas = DB::select("SELECT post_id,meta_key,meta_value
		from wp_postmeta 		   
		WHERE 
			post_id IN (".implode(',',$post_ids).")
			");
		$users = User::whereIn('ID',$user_ids)->get()->toArray();
		
		foreach($posts as &$post){
			$post = (object)$post;
			$post->path = self::getKeyOfPost($metas,$post->ID,'trailer_video');
			$post->thumbnail_path = self::getKeyOfPost($metas,$post->ID,'_video_thumbnail');
			$post->number_like = self::getKeyOfPost($metas,$post->ID,'_video_network_likes');
			$post->number_comment = self::getKeyOfPost($metas,$post->ID,'_video_network_comments');
			$post->view = self::getKeyOfPost($metas,$post->ID,'_video_network_views');
			$post->user = array_filter($users,function ($a) use ($post){
				return $post->post_author==$a['ID'];
			})[0];
		}				
		
		return $posts;
	}

	

    public static function getVideo($page = 0){
		$offset = $page*5;
        $posts = self::orderBy('ID','DESC')
		->isActive()
		->skip($offset)->take(5)->get()->toArray();
		return self::fetchMetaKey($posts);
		
    }

	public static function updateCommentCount($video_id,$up = true){
		$view = self::getMeta($video_id,'_video_network_comments');
		// echo $view;die;
		if($up)
        	$view++;
		else{
			$view = max(--$view,0);
		}

        return self::updateMeta($video_id,'_video_network_comments',$view);
	}

	public static function updateLikeCount($video_id){
		$count = DB::table('wp_postmeta')->where('post_id',$video_id)->count();
		self::updateMeta($video_id,'_video_network_likes',$count);
		return $count;
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
