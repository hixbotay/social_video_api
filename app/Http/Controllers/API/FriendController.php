<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FriendRelation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, User $user)
    {

      return response(['data' => User::friendWith($user, $request->user())->selectList()->paginate(20)->items()]);

    }
	
	public function followingUser(Request $request, User $user)
    {
		
      return response(['data' => User::followUser($user,$request->user())->selectList()->paginate(20)->items()]);

    }
	
	public function whoFollowMe(Request $request, User $user)
    {

      return response(['data' => User::whoFollowMe($user,$request->user())->selectList()->paginate(20)->items()]);

    }

    
    /**
     * delete friend
     */
    public function destroy(Request $request, User $user)
    {
        $currentUser = $request->user();
        try{
        DB::transaction(function () use ($user, $currentUser) {
          
          $friend_relation = FriendRelation::where([
            'from_user_id' => $user->ID,
            'to_user_id' => $currentUser->ID
          ])->delete();
          $friend_relation = FriendRelation::where([
            'from_user_id' => $currentUser->ID,
            'to_user_id' => $user->ID
          ])->delete();
          
          $user->updateFollowMe();
          $currentUser->updateFollowMe();
          
        }, 5);
      }catch(\Exception $e){
          
      }

		return response(['message'=>'Delete friend success']);
    }
}
