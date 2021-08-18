<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class WordpressComment extends Model
{
    use HasFactory;
	protected $table = 'wp_comments';
    public $timestamps = false;
	protected $primaryKey = 'comment_ID';

    protected $casts = [
    ];

    protected $fillable = [
        'comment_ID',
        'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_IP', 'comment_date','comment_date_gmt', 'comment_content', 'comment_approved', 'comment_parent', 'user_id'
    ];

    protected $hidden = [
        // 'path',
    ];

	public static function updateMeta($post_id,$meta_key,$value){
		return DB::table('wp_commentmeta')
		->where('comment_id', $post_id)
		->where('meta_key',$meta_key)
		->update(['meta_value' => $value]);
	}

	public static function getMeta($post_id,$meta_key=''){
		$query = DB::table('wp_commentmeta')
		->where('comment_id', $post_id);
		if($meta_key){
			$query->where('meta_key',$meta_key);
		}
		$data = $query->get()->toArray();
		if($meta_key){
			return $data[0]->meta_value;
		}else{
			$result = [];
			foreach($data as $arr){
				$result[$arr->meta_key] = $arr->meta_value;
			}
			return $result;
		}
	}


	public static function fetchMetaKey($posts){
		$post_ids = array_map ( function ($a) {
			return $a['ID'];
		}, $posts );
		$user_ids = array_map ( function ($a) {
			return $a['post_author'];
		}, $posts );
		
		$metas = DB::table("wp_commentmeta")->whereIn('comment_id',$post_ids);
		$users = User::whereIn('ID',$user_ids)->get()->toArray();
		
		foreach($posts as &$post){
			$post = (object)$post;
			$post->number_like = count(json_decode(self::getKeyOfPost($metas,$post->ID,'like')));
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
	
	static function getKeyOfPost($metas,$post_id,$key){
		foreach($metas as $meta){
			if($meta->post_id==$post_id && $meta->meta_key==$key){
				return $meta->meta_value;
			}
		}
	return '';
	}

	public function user(){
        return $this->belongsTo(User::class,'user_id','ID');
    }
	public function meta(){
        return $this->belongsTo(User::class,'comment_ID','comment_id');
    }
}
