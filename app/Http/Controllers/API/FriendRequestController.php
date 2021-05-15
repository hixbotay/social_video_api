<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FriendRequest;
use App\Models\User;
use App\Models\FriendRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FriendRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
		return response(['data' => FriendRequest::with('from_user')->where('to_user_id',$request->user()->ID)->paginate(20)->items()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([ 
            'to_user_id' => 'required|exists:wp_users,ID',
            ]);
        if($request->user()->ID != $request->input('to_user_id')){
            FriendRequest::firstOrCreate([
                'from_user_id' => $request->user()->ID,
                'to_user_id' => $request->input('to_user_id')
            ]);
			return response(['message'=>'Send request success']);
        }else{
			return response(['message'=>'Invalid request'],400);
		}
		#@todo notify
		
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FriendRequest  $friendRequest
     * @return \Illuminate\Http\Response
     */
    public function accept(Request $request)
    {
       $validatedData = $request->validate([ 
            'from_user_id' => 'required|exists:api_friend_requests',
            ]);
        $user = $request->user();
        $friendRequest = FriendRequest::where('from_user_id',$validatedData['from_user_id'])
        ->where('to_user_id',$user->ID)->first();
        if(!$friendRequest){
            return response(['message'=>'Invalid friend request'],400);
        }
		DB::transaction(function () use ($validatedData, $user, $friendRequest) {
            $friendRequest->delete();
 
            FriendRelation::create([
                'from_user_id' => $user->ID,
                'to_user_id' => $validatedData['from_user_id'],
                'is_friend' => true,
                'is_follow' => true,
            ]);
			FriendRelation::create([
                'from_user_id' => $validatedData['from_user_id'],
                'to_user_id' => $user->ID,
                'is_friend' => true,
                'is_follow' => true,
            ]);
			$user->updateFollowMe();
			User::find($validatedData['from_user_id'])->updateFollowMe();
            return $user;
        }, 5);

		return response(['message'=>'Accept request success']);
    }
    
    public function decline(Request $request)
    {
       $validatedData = $request->validate([ 
            'from_user_id' => 'required|exists:api_friend_requests',
            ]);
		$user = $request->user();
		FriendRequest::where('from_user_id',$validatedData['from_user_id'])
            ->where('to_user_id',$user->ID)->delete(); 

		return response(['message'=>'Decline request success']);
    }


    /**
     * Cancel friend request
     */
    public function cancel(Request $request)
    {
        $validatedData = $request->validate([ 
            'to_user_id' => 'required|exists:api_friend_requests',
            ]);
		$user = $request->user();
		FriendRequest::where('from_user_id',$user->ID)
            ->where('to_user_id',$validatedData['to_user_id'])->delete(); 

		return response(['message'=>'Cancel request success']);
    }
	
	public function follow(Request $request)
    {
       $validatedData = $request->validate([ 
            'user_id' => 'required|exists:wp_users,ID',
            ]);
        $user = $request->user();
        
		DB::transaction(function () use ($validatedData, $user) {
             
            $friend_relation = FriendRelation::firstOrCreate([
                'from_user_id' => $user->ID,
                'to_user_id' => $validatedData['user_id'],
                
            ]);
			if(!$friend_relation->is_follow){
				$friend_relation->is_follow = true;
				$friend_relation->save();
			}
			
			User::find($validatedData['user_id'])->updateFollowMe();
			$user->updateFollowMe();
            return $user;
        }, 5);

		return response(['message'=>'Follow request success']);
    }
	
	public function unFollow(Request $request)
    {
       $validatedData = $request->validate([ 
            'user_id' => 'required|exists:wp_users,ID',
            ]);
        $user = $request->user();
        
		DB::transaction(function () use ($validatedData, $user) {
             
            $friend_relation = FriendRelation::firstOrCreate([
                'from_user_id' => $user->ID,
                'to_user_id' => $validatedData['user_id']
            ]);
			
			if($friend_relation->is_follow){
				$friend_relation->is_follow = false;
				$friend_relation->save();
			}
			
			User::find($validatedData['user_id'])->updateFollowMe();
			$user->updateFollowMe();
            return $user;
        }, 5);

		return response(['message'=>'Unfollow request success']);
    }
}
