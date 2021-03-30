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
        }
		#@todo notify
		return response(['message'=>'Send request success']);
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
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FriendRequest  $friendRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(FriendRequest $friendRequest)
    {
        //
    }
}
