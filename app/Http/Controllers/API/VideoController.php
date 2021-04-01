<?php
 
namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Models\VideoComment;
use App\Models\VideoLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Enums\VideoStatusEnum;
use Illuminate\Support\Facades\DB;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $videos = VideoResource::collection(Video::with('user')->where('user_id', '=', $request->user()->ID)->latest()->paginate(5));
 
        return response(['data'=>$videos]);
    } 

    //get video from friend
    public function getFriendVideo(User $user)
    {
        //
        $videos = VideoResource::collection(Video::with('user')->where('user_id', '=', $user->ID)->isActive()->latest()->paginate(5));
 
        return response(['data'=>$videos]);
    } 
	
	//get video on comunity tab
	public function getNewFeedTab(Request $request)
    {
        //
        $videos = VideoResource::collection(Video::with('user')->newFeedTab($request->user())->latest()->paginate(5)->items());
 
        return response(['data'=>$videos]);
    }
	//get video on TV tab
	public function getNewFeedTv(Request $request)
    {
        //
        
        $videos = VideoResource::collection(Video::with('user')->isActive()->latest()->paginate(5));
 
        return response(['data'=>$videos]);
    }
	
	
 
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validData = $request->validate([
            'title' => 'required',
            'description' => 'required',
            'path' => 'required|mimes:mp4|max:100000',
			'status' => 'required|in:' . implode(',', VideoStatusEnum::getAllValue())
            ]);
        $user = $request->user();
        $path = Storage::disk()->put($user->ID, $request->path);
        $validData['user_id'] = $request->user()->ID;
        $validData['path'] = $path;
        $validData['thumbnail_path'] = '';
        $validData['view'] = 0;
        $result = new VideoResource(Video::create($validData));
        return response(['data'=>$result, 'message'=> 'Video is created']);
    }
 
 
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(Video $video)
    {
        //
		$result = new VideoResource($video);
        return response(['data'=>$result]);
    }
 
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Video $video)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
 
        $video->update($request->all());
        return response(['data'=>new VideoResource($video), 'message'=> 'Video is updated']);
    }
	//user viewed a video
	public function viewed(Request $request, Video $video)
    {        
		$video->view +=1;
		$video->save();
        return response(['data'=>$video->view, 'message'=> 'Video is updated']);
    }
 
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Video $video)
    {
        if($video->user_id != $request->user()->ID){
            return response()->json(['message' => 'You can only delete your own video.'], 403);
        }
        Storage::delete($video->path);
        $video->delete();
         
        return response(['message'=> 'Video is deleted']);
    }
	
	public function getComment(Video $video)
    {
        //
        $videos = VideoComment::with('user')->where('video_id', '=', $video->id)->get();
 
        return response(['data'=>$videos]);
    } 
	
	public function addComment(Request $request)
    {
        $validData = $request->validate([
			'comment' => 'required|max:500',
            'video_id' => 'required',
			'parent_id' => 'exists:api_video_comments,id'
            ]);
        $user = $request->user();
		$validData['user_id'] = $user->ID;
		$comment = DB::transaction(function () use ($validData, $user) {
			
            $result = VideoComment::create($validData);
			
			Video::find($validData['video_id'])->updateCommentCount();
            return $result;
        }, 5);
        
        return response(['data'=>$comment, 'message'=> 'Add comment success']);
    }
	
	public function deleteComment(Request $request, VideoComment $comment)
    {
		
        if($comment->user_id != $request->user()->ID){
            return response()->json(['message' => 'You can only delete your own comment.'], 403);
        }
       		
		DB::transaction(function () use ($comment) {
			
			$video_id = $comment->video_id;
            $comment->delete();
			Video::find($video_id)->updateCommentCount();
        }, 5);
         
        return response(['message'=> 'Comment is deleted']);
    }
	
	public function getLike(Video $video)
    {
        //
        $data = VideoLike::with('user')->where('video_id', '=', $video->id)->get();
 
        return response(['data'=>$data]);
    } 
	
	public function addLike(Request $request)
    {
        $validData = $request->validate([
            'video_id' => 'required|exists:api_videos,id'
            ]);
        $user = $request->user();
		$validData['user_id'] = $user->ID;
		if(VideoLike::where('video_id',$validData['video_id'])->where('user_id',$user->ID)->count()){
			return response(['message'=> 'Liked']);
		}
		$likeCount = DB::transaction(function () use ($validData, $user) {
			
            $result = VideoLike::create($validData);
			
			$count = Video::find($validData['video_id'])->updateLikeCount();
            return $count;
        }, 5);
        
        return response(['data'=>$likeCount, 'message'=> 'Liked']);
    }
	
	public function unLike(Request $request, Video $video)
    {
		$user = $request->user();
		DB::transaction(function () use ($video,$user) {
			
			VideoLike::where('video_id',$video->id)->where('user_id',$user->ID)->delete();
            
			Video::find($video->id)->updateLikeCount();
        }, 5);
         
        return response(['message'=> 'Unliked']);
    }
}