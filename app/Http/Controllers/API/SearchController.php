<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    //
    public function search(Request $request){
		$request->validate([
            'key' => 'required',
			'friend' =>'boolean'
            ]);
		
        $key = $request->query('key');
        $type = $request->query('type');
		
        return response(
			[
			'users' => $type == 'user' || $type == '' ? User::search($key, $request->query('is_friend') ? $request->user()->ID : false)->paginate(5)->items() : [],
			'videos' => $type == 'video' || $type == '' ? VideoResource::collection( Video::search($key)->paginate(5)->items() ) : []
			]); 
    }
}
