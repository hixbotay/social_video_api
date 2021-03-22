<?php
namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    // use HasProfilePhoto;
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
        'user_nicename',
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
        // 'user_registered' => 'datetime',
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

    public function getPhotoPathAttribute(){
        return Storage::url($this->meta->photo_path);
    }

    public function getNumberFriendAttribute(){
        return $this->meta->number_friend;
    }
    public function getNumberFollowAttribute(){
        return $this->meta->number_follow;
    }
    public function getNumberFollowMeAttribute(){
        return $this->meta->number_follow_me;
    }
    public function getBirthdayAttribute(){
        return $this->meta->birthday;
    }
 
}