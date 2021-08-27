<?php
 
namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Http\Resources\WordpressVideoResource;
use App\Models\Video;
use App\Models\WordpressPost;
use Illuminate\Http\Request;
use App\Http\Enums\VideoTypeEnum;
use App\Models\Favorite;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $favorite = Favorite::where('user_id',$request->user()->ID)->get();
 
        return response(['data'=>$favorite]);
    } 

    public function store(Request $request)
    {
        $validData = $request->validate([
            'name' => 'required',
            ]);
        $user = $request->user();
        $validData['user_id'] = $user->ID;
        $validData['list'] = [];
        $result = Favorite::create($validData);
        return response(['data'=>$result, 'message'=> 'Favorite list is created']);
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Favorite $favorite)
    {
        $request->validate([
            'name' => 'required',
        ]);
 
        $favorite->update($request->all());
        return response(['data'=>$favorite, 'message'=> 'List name is updated']);
    }
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Favorite $favorite)
    {
        if($favorite->user_id != $request->user()->ID){
            return response()->json(['message' => 'You can only delete your own.'], 403);
        }
        $favorite->delete();
         
        return response(['message'=> 'Favorite list is deleted']);
    }
	
	public function show(Favorite $favorite)
    {
		$tv_ids = [];
        $normal_ids = [];
        foreach($favorite->list as $v){
            if($v['video_type'] == VideoTypeEnum::TV['value']){
                $tv_ids[] = $v['video_id'];
            }else{
                $normal_ids[] = $v['video_id'];
            }
        }
        $tvs = [];
        $normals = [];
        if(count($tv_ids)) $tvs = WordpressVideoResource::collection(WordpressPost::with('user')->whereIn('ID', $tv_ids)->get())->toArray(0);
        if(count($normal_ids)) $normals = VideoResource::collection(Video::with('user')->whereIn('id', $normal_ids)->get())->toArray(0);

		$videos = [];
        //map with current list ordering
		foreach($favorite->list as $v){
            if($v['video_type'] == VideoTypeEnum::TV['value']){
                $video = array_filter($tvs,function ($e) use ($v){
                    return $e['id'] == $v['video_id'];
                });
                
            }else{
                $video = array_filter($normals,function ($e) use ($v){
                    return $e['id'] == $v['video_id'];
                });
            }
            if(count($video)){
                $videos[] = reset($video);
            }
        }
 
        return response(['data'=>$videos]);
    } 
	
	public function addVideo(Request $request, Favorite $favorite)
    {
        $validData = $request->validate([
            'video_id' => 'required',
			'video_type' => 'required|in:'.implode(',',VideoTypeEnum::getAllValue()),
            ]);
        $user = $request->user();
		if($favorite->user_id != $user->ID){
            return response()->json(['message' => 'You can only add your own.'], 403);
        }
        if($request->video_type == VideoTypeEnum::TV['value']){
            if(!WordpressPost::find($request->video_id)){
                return response()->json(['message' => 'Video not exist in TV tab'], 404); 
            }
        }
        if($request->video_type == VideoTypeEnum::NORMAL['value']){
            if(!Video::find($request->video_id)){
                return response()->json(['message' => 'Video not exist in Yabantu tab'], 404); 
            }
        }
        $list = $favorite->list;
        $list[] = [
            'video_id' => $request->video_id,
            'video_type' => $request->video_type,
        ];
        $favorite->list = $list;
		$favorite->update();

        return response(['message'=> 'Add Video success']);
    }
	
	public function deleteVideo(Request $request, Favorite $favorite)
    {
		$request->validate([
            'video_id' => 'required',
			'video_type' => 'required|in:'.implode(',',VideoTypeEnum::getAllValue()),
            ]);
        $user = $request->user();
		if($favorite->user_id != $user->ID){
            return response()->json(['message' => 'You can only delete your own.'], 403);
        }
        $list = $favorite->list;
        foreach($list as $i=>$v){
            if($v['video_type'] == $request->video_type && $v['video_id'] == $request->video_id){
                unset($list[$i]);
                break;
            }
        }
        $favorite->list = array_values($list);
		$favorite->update();

        return response(['message'=> 'Delete Video success']);
    }
	
}