<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use App\Models\UserMeta;
use App\Models\FriendRequest;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    // use TwoFactorAuthenticatable;
    
    protected $table = 'wp_users';
    protected $primaryKey = 'ID'; // or null
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_nicename', 'user_login','user_email', 'user_pass','display_name','user_registered','user_pass'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'user_pass',
        'user_status',
        'user_activation_key',
        'user_url',
        //'user_nicename',
        'meta'
        // 'two_factor_recovery_codes',
        // 'two_factor_secret',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        
    ];
    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'photo_path','number_friend','number_follow','number_follow_me','birthday','is_verify','setting'
    ];

    public function meta()
    {
        return $this->belongsTo(UserMeta::class,'ID','user_id');
    }
	
	public static function search($key, $is_friend_with = false){
		
		$query = self::where(function($query) use ($key){
				$query->where('user_nicename','like',"%{$key}%")
				->orWhere('user_login',$key)
				->orWhere('user_email',$key)
				->orWhere('display_name','like',"%{$key}%");
			})->orderBy('user_nicename');
		if($is_friend_with){
			$query->leftJoin('api_friend_relations as r', 'wp_users.ID', '=', 'r.from_user_id')
			->where('r.to_user_id',$is_friend_with)
			->where('r.is_friend',true)
			->groupBy('wp_users.ID');
		}
		return $query;
		
    }

	public function scopeFriendWith($query, $from_user, $current_user){
        $query = $query->join('api_friend_relations as r', 'wp_users.ID', '=', 'r.to_user_id')
		->where('r.from_user_id',$from_user->ID)
		->where('r.is_friend',true)
		->groupBy('wp_users.ID');
		if(!$current_user){
			$current_user = (object)['ID'=>0];
		}
		$query->leftJoin('api_friend_relations as r_w_current', function ($join) use($current_user){
				$join->on('r_w_current.to_user_id', '=', 'r.to_user_id');
				$join->on('r_w_current.from_user_id', '=', DB::raw($current_user->ID));
			});
    }
	public function getFriendRelation($from){
		if(!$from){
			return $this;
		}
        $user = $this;
        $relation = User::leftJoin('api_friend_relations as r', 'wp_users.ID', '=', 'r.from_user_id')
		->where('r.to_user_id',$user->ID)
		->where('r.from_user_id',$from->ID)
        ->select(['r.is_friend','r.is_follow'])
        ->first();
        $user->is_friend = $relation? $relation->is_friend : false;
        $user->is_follow = $relation? $relation->is_follow : false;
		
		$user->is_waiting_friend_request = FriendRequest::where('from_user_id',$from->ID)->where('to_user_id',$this->ID)->count() ? 'waiting_accept' : (FriendRequest::where('to_user_id',$from->ID)->where('from_user_id',$this->ID)->count() ? 'received_request' : false);
        return $user;
        
    }

	public function scopeFollowUser($query, $from_user, $current_user){
		
        $query = $query->leftJoin('api_friend_relations as r', 'wp_users.ID', '=', 'r.to_user_id')
		->where('r.from_user_id',$from_user->ID)
		->where('r.is_follow',true)
		->groupBy('wp_users.ID');
		if(!$current_user){
			$current_user = (object)['ID'=>0];
		}
		$query->leftJoin('api_friend_relations as r_w_current', function ($join) use($current_user){
				$join->on('r_w_current.to_user_id', '=', 'r.to_user_id');
				$join->on('r_w_current.from_user_id', '=', DB::raw($current_user->ID));
			});
			//echo $query->toSql();die;
		return $query;
    }
	
	public function scopeWhoFollowMe($query, $to_user, $current_user){
		if(!$current_user){
			$current_user = (object)['ID'=>0];
		}
        $query = $query->leftJoin('api_friend_relations as r', 'wp_users.ID', '=', 'r.from_user_id')
		->where('r.to_user_id',$to_user->ID)
		->where('r.is_follow',true)
		->groupBy('wp_users.ID');
		$query->leftJoin('api_friend_relations as r_w_current', function ($join) use($current_user){
				$join->on('r_w_current.to_user_id', '=', 'r.from_user_id');
				$join->on('r_w_current.from_user_id', '=', DB::raw($current_user->ID));
			});
			
		return $query;
    }
	
	public function scopeSelectList($query){
		return $query->select(['wp_users.ID','wp_users.user_login','wp_users.user_nicename','wp_users.user_email','wp_users.display_name',DB::Raw('IFNULL( r_w_current.is_friend, 0) as is_friend'),DB::Raw('IFNULL( r_w_current.is_follow, 0) as is_follow')]);
	}

	public function updateFollowMe(){
		if(!$this->meta){
			$this->meta = UserMeta::create([
                'user_id' => $this->ID,
                'is_verify' => 0,
                'photo_path' => '',
                'verify_photo' => '',
                'fb_id' => '',
                'birthday' => '',
				'number_follow_me'=> FriendRelation::where('to_user_id',$this->ID)->where('is_follow',true)->count(),
				'number_friend' => FriendRelation::where('to_user_id',$this->ID)->where('is_friend',true)->count(),
				'number_follow' => FriendRelation::where('from_user_id',$this->ID)->where('is_follow',true)->count(),
            ]);
		}
		$this->meta = UserMeta::where('user_id', $this->ID)
              ->update([
				'number_follow_me' => FriendRelation::where('to_user_id',$this->ID)->where('is_follow',true)->count(),
				'number_friend' => FriendRelation::where('to_user_id',$this->ID)->where('is_friend',true)->count(),
				'number_follow' => FriendRelation::where('from_user_id',$this->ID)->where('is_follow',true)->count(),
				]);
		return $this;
    }
    
    // public function is_friend($user){
    //     return $query->leftjoin('api_friend_relations as r', 'wp_users.ID', '=', 'r.from_user_id')
	// 	->where('r.to_user_id',$user->ID)
	// 	->where('r.is_follow',true);
    // }

    public function getPhotoPathAttribute(){
        return $this->getMetaKey('photo_path') ? Storage::url($this->getMetaKey('photo_path')) : $this->getDefaultPhoto();
    }

    public function getNumberFriendAttribute(){
        return (int)$this->getMetaKey('number_friend');
    }
    public function getNumberFollowAttribute(){
        return (int)$this->getMetaKey('number_follow');
    }
    public function getNumberFollowMeAttribute(){
        return (int)$this->getMetaKey('number_follow_me');
    }
    public function getBirthdayAttribute(){
        return $this->getMetaKey('birthday');
    }
    public function getisVerifyAttribute(){
        return $this->getMetaKey('is_verify');
    }
	
	public function getSettingAttribute(){
        return [
			'max_photo_size' => config('filesystems.max_size'),
			'max_video_size' => config('filesystems.max_video_size')
		];
    }
	
	private function getMetaKey($attr){
		if($this->meta){
			return $this->meta->$attr;
		}
		return '';
	}
	
	private function getDefaultPhoto(){
		return 'https://ui-avatars.com/api/?name='.urlencode($this->display_name).'&color=7F9CF5&background=EBF4FF';
	}
	
 
}