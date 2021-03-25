<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;
use App\Models\UserMeta;

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
        'photo_path','number_friend','number_follow','number_follow_me','birthday'
    ];

    public function meta()
    {
        return $this->belongsTo(UserMeta::class,'ID','user_id');
    }
	
	public static function search($key){
        return self::orWhere(function($query) use ($key){
                $query->where('user_nicename','like',"%{$key}%")
				->orWhere('user_login',$key);
            })->orderBy('user_nicename');
    }

	public function scopeFriendWith($query, $user){
        return $query->join('api_friend_relations as r', 'wp_users.ID', '=', 'r.to_user_id')
		->where('r.from_user_id',$user->ID)
		->where('r.is_friend',true);
    }

	public function scopeFollowUser($query, $user){
        return $query->join('api_friend_relations as r', 'wp_users.ID', '=', 'r.to_user_id')
		->where('r.from_user_id',$user->ID)
		->where('r.is_follow',true);
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
				'number_follow_me'=> FriendRelation::where('to_user_id',$this->ID)->where('is_follow',true)->count()
            ]);
		}
		$this->meta = UserMeta::where('user_id', $this->ID)
              ->update(['number_follow_me' => FriendRelation::where('to_user_id',$this->ID)->where('is_follow',true)->count()]);
		return $this;
	}

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