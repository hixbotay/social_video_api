<?php
 
namespace App\Models;

use App\Http\Enums\NotifyEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Enums\VideoStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Notify extends Model
{
    use HasFactory;
    protected $table = 'api_notifications';
    public $timestamps = true;
 
    protected $casts = [
        'content' => 'array'
    ]; 
 
    protected $fillable = [
	'id','user_id','is_read','type','created_at','updated_at','content'
    ];

    protected $appends = [
        'type_string'
    ];
 
    public function user(){
        return $this->belongsTo(User::class,'user_id','ID');
    }
	
	public function scopeIsRead($query){
		return $query->where('is_read' ,1);
	}
    public function scopeUnRead($query){
		return $query->where('is_read' ,0);
	}

	public function scopeUser($query, $user_id){
        return $query->where('user_id',$user_id);
    }
	
	public function updateRead(){
		return $this->update(['is_read' => 1 ]);
	}

    public static function addNotify($data){
        // debug($data);die;
        $add = $data;
        if(is_array($data['user_id'])){
            foreach($data['user_id'] as $user_id){
                $add['user_id'] = $user_id;
                self::create($add);
            }
        }else{
            self::create($add);  
            $data['user_id'] = array($data['user_id']);
        }
        $FcmToken = UserMeta::whereIn('user_id',$data['user_id'])->pluck('device_token')->all();

        $url = 'https://fcm.googleapis.com/fcm/send';
        
        $serverKey = config('app.firebase_key');
        $FcmToken = array_filter($FcmToken,function ($e){
            return $e != '';
        });
        if(count($FcmToken) == 0){
            return false;
        }
        $data = [
            "registration_ids" => $FcmToken,
            "notification" => [
                "title" => $data['content']['msg'],
                "body" => $data['content']['msg'],  
            ]
        ];
        $encodedData = json_encode($data);
    
        $headers = [
            'Authorization:key=' . $serverKey,
            'Content-Type: application/json',
        ];
    
        $ch = curl_init();
      
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encodedData);

        // Execute post
        $result = curl_exec($ch);

        Log::debug('app.notification', ['Send notify: ' . curl_error($ch).' '.$result]);
        curl_close($ch);
        
        return true;
        // return self::create($data);
    }

    public function getTypeStringAttribute(){
        return NotifyEnum::getDisplay($this->type);
    }

}