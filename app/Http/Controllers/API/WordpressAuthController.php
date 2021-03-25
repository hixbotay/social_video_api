<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserMeta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use MikeMcLin\WpPassword\Facades\WpPassword;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;

class WordPressAuthController extends Controller
{
    public function getCurrentuser(Request $request){
        $user = $request->user();
        return response([ 'user' => $user]);
    }

    public function show(User $user){
        return response([ 'user' => $user]);
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'user_nicename' => 'required|max:200',
            'user_email' => 'email|required|unique:wp_users',
            'user_password' => 'required',
            'user_login' => 'required|unique:wp_users'
        ]);
        
		$validatedData['display_name'] = $validatedData['user_nicename'];
		$validatedData['user_registered'] = Carbon::now()->toDateTimeString();
        $validatedData['user_pass'] = WpPassword::make($validatedData['user_password']);
        $user = DB::transaction(function () use ($validatedData) {
            $user = User::create($validatedData);
 
            UserMeta::create([
                'user_id' => $user->ID,
                'is_verify' => 0,
                'photo_path' => '',
                'verify_photo' => '',
                'fb_id' => '',
                'birthday' => '',
            ]);
            return $user;
        }, 5);
        $accessToken = $user->createToken('authToken')->plainTextToken;
        return response([ 'user' => $user, 'access_token' => $accessToken]);
    }

    public function login(Request $request)
    {
        $loginData = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);
        $user = User::where('user_email', $request->email)->first();
        if ( !WpPassword::check($request->password, $user->user_pass) ) {
            return response([ 'message' => 'Invalid password'],400);
        }
        $accessToken = $user->createToken('authToken')->plainTextToken;
        return response(['user' => $user, 'access_token' => $accessToken]);
    }
	/* redirect to facebook to get token
	public function facebookRedirect()
    {
        return Socialite::driver('facebook')->redirect();
    }
	*/
	
	
	
	public function loginSocial(Request $request)
    {
		$validatedData = $request->validate([
			'type' => 'required|max:200',
			'token' => 'required'
		]);
        try {    
            $userSocial = Socialite::driver($validatedData['type'])->userFromToken($validatedData['token']);
            //$isUser = User::where('fb_id', $user->id)->first();
			if(empty($userSocial->email)){
				return response([ 'message' => 'Please public email on profile '.$validatedData['type']],400);
			}
			$user = User::where('user_email', $userSocial->email)->first();
			
            if(!$user){
				$user = DB::transaction(function () use ($validatedData) {
					$user = User::create([
						'display_name' => $userSocial->name,
						'user_nicename' => $userSocial->name,
						'user_email' => $userSocial->email,
						'user_login' => $userSocial->email,
						'user_pass' => WpPassword::make('Koph4iem1324')
					]);
		 
					UserMeta::create([
						'user_id' => $user->ID,
						'is_verify' => 0,
						'photo_path' => '',
						'verify_photo' => '',
						'fb_id' => '',
					]);
					return $user;
				}, 5);           
            }
    
        } catch (\Exception $exception) {
            return response([ 'message' => $exception->getMessage()],400);
        }
		
		$accessToken = $user->createToken('authToken')->plainTextToken;
        return response(['user' => $user, 'access_token' => $accessToken]);
    }
	
	

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();
        return response(['message' => 'Logout Success']);
    }

    public function update(Request $request)
    {
        $validatedData = $request->validate([
			'display_name' => 'max:200',
            'birthday' => 'max:10'
        ]);
        $user = $request->user();
        
        if($request->user_password)
            $validatedData['user_pass'] = WpPassword::make($validatedData['user_password']);

        DB::transaction(function () use ($validatedData,&$user) {
            $user->update($validatedData);
            $meta = UserMeta::find($user->ID);
            if($meta){
                $meta->update($validatedData);
            }else{
                $validatedData['user_id'] = $user->ID;
                $validatedData['photo_path'] = '';
                $validatedData['verify_photo'] = '';
                $validatedData['is_verify'] = 0;
                UserMeta::create($validatedData);
            }
        }, 5);  
        return response([ 'user' => $user]);
    }
    public function uploadProfilePhoto(Request $request)
    {
        $request->validate([
			'photo_path' => 'required|max:'.config('filesystems.max_size'),
        ]);
        $user = $request->user();
        $path = Storage::disk()->put($user->ID.'/profile-photo', $request->photo_path);
        $meta = UserMeta::find($user->ID);
        if($meta->photo_path && Storage::disk()->exists($meta->photo_path))
            Storage::disk()->delete($meta->photo_path);
        $meta->photo_path = $path;
        $meta->save();
        return response([ 'user' => $user]);
    }


}