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
            ]);
		
        $key = $request->query('key');
        return response(['users'=>User::search($key)->paginate(5)->items(),'videos' => VideoResource::collection( Video::search($key)->paginate(5)->items() )]); 
    }
}
