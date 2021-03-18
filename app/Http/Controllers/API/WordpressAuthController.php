<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserMeta;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use MikeMcLin\WpPassword\Facades\WpPassword;
use Socialite;
use Illuminate\Support\Facades\DB;

class WordPressAuthController extends Controller
{
    public function getCurrentuser(Request $request){
        $user = $request->user();
        $user->number_friend = 10;
        $user->number_follow = 15;
        $user->avatar = 'https://scontent.fhan5-2.fna.fbcdn.net/v/t1.0-0/s180x540/15781738_1240652895996367_1958146021700446211_n.jpg?_nc_cat=102&ccb=1-3&_nc_sid=9267fe&_nc_ohc=weza5MDyFL8AX83VlTg&_nc_ht=scontent.fhan5-2.fna&tp=7&oh=6d3c8c48278585eb32b467f5ab2a6e55&oe=6072829A';
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
        if($validatedData->errors()){
            return response($validatedData,400);
        }
		$validatedData['display_name'] = $validatedData['user_nicename'];
		$validatedData['user_registered'] = Carbon::now()->toDateTimeString();
        $validatedData['user_password'] = WpPassword::make($validatedData['user_password']);
        $user = DB::transaction(function () use ($validatedData) {
            $user = User::create($validatedData);
 
            UserMeta::create([
                'user_id' => $user->ID,
                'is_verified' => false,
                'photo_path' => ''
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
		try{
            $validatedData = $request->validate([
                'type' => 'required|max:200',
                'token' => 'required'
            ]);
        }
        catch(\Exception $e){
            return response([ 'message' => $e->getMessage()],400);
        }
        try {    
            $userSocial = Socialite::driver($validatedData['type'])->userFromToken($validatedData['token']);
            //$isUser = User::where('fb_id', $user->id)->first();
			if(empty($userSocial->email)){
				return response([ 'message' => 'Please public email on profile '.$validatedData['type']],400);
			}
			$user = User::where('user_email', $user->email)->first();
			
            if(!$user->ID){
                $user = User::create([
                    'display_name' => $userSocial->name,
					'user_nicename' => $userSocial->name,
                    'email' => $userSocial->email,
                    'password' => WpPassword::make($validatedData['user_password'])
                ]);
                $meta = UserMeta::create([
                    'user_id' => $user->ID,
                    'fb_id' => $userSocial->id,
                ]);

                
    
            }
    
        } catch (Exception $exception) {
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
        try{
            $validatedData = $request->validate([
                'user_nicename' => 'max:200',
            ]);
        }
        catch(\Exception $e){
            return response([ 'message' => $e->getMessage()],400);
        }
        $user = $request->user();
        
        if($validatedData['user_password'])
            $validatedData['user_password'] = WpPassword::make($validatedData['user_password']);
        $user = $user->update($validatedData);
        return response([ 'user' => $user]);
    }
    public function uploadProfilePhoto(Request $request)
    {
        try{
            $max_file_size = '10000000';
            $validatedData = $request->validate([
                'photo' => 'require|max:'.$max_file_size,
            ]);
        }
        catch(\Exception $e){
            return response([ 'message' => 'File size must < '.$max_file_size.'kb'],400);
        }
        // $request->photo->saveAs();
        $user = $request->user();
        
        // $user = $user->update($validatedData);
        return response([ 'user' => $user]);
    }


}