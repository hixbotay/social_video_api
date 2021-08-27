<?php
 
namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Enums\NotifyEnum;
use App\Http\Resources\WordpressVideoResource;
use App\Models\Video;
use App\Models\WordpressPost;
use App\Models\VideoComment;
use App\Models\VideoLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Enums\VideoStatusEnum;
use App\Http\Resources\CommentResource;
use App\Models\Notify;
use App\Models\WordpressComment;
use App\Models\WordpressLike;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VideoTvController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $videos = WordpressPost::getVideo(0);
        return response(['data'=>WordpressVideoResource::collection($videos)]);
    } 
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Video  $video
     * @return \Illuminate\Http\Response
     */
    public function show(WordpressPost $video)
    {
        //
		$result = new WordpressVideoResource($video);
        return response(['data'=>$result]);
    }

    //get video from friend
    public function getFriendVideo(User $user)
    {
        $videos = WordpressVideoResource::collection(WordpressPost::where('post_author', '=', $user->ID)->isActive()->latest()->paginate(5));
 
        return response(['data'=>$videos]);
    } 
	//get video on TV tab
	public function getNewFeedTv(Request $request, $page)
    {
		$videos = WordpressPost::getVideo($page);
        return response(['data'=>WordpressVideoResource::collection($videos)]);
    }
	
    //user viewed a video
	public function viewed(Request $request, WordpressPost $video)
    {        
        $view = WordpressPost::getMeta($video->ID,'_video_network_views');
        $view++;
        WordpressPost::updateMeta($video->ID,'_video_network_views',$view);
        return response(['data'=>$view, 'message'=> 'Video is updated']);
    }
 
    public function getComment(WordpressPost $video)
    {
		$videos = WordpressComment::with('user')->where('comment_post_ID', '=', $video->ID)->where('comment_parent','=',0)->paginate(20)->items();
		$video_ids = array_map ( function ($a) {
				return $a['comment_ID'];
			}, $videos );
		
		$video_children = (array)WordpressComment::with('user')->whereIn('comment_parent', $video_ids)->get()->toArray();
		
		foreach($videos as &$v){
			$v['children'] = array_values(array_filter($video_children,function ($a) use ($v) {return $a['comment_parent'] == $v['comment_ID'];}));
		}
 
        return response(['data'=>CommentResource::collection($videos)]);
    } 
	
	public function addComment(Request $request)
    {
        $request->validate([
			'comment' => 'required|max:500',
            'video_id' => 'required',
			// 'parent_id' => 'exists:api_video_comments,id'
            ]);
        $user = $request->user();
		$comment = DB::transaction(function () use ($request, $user) {
			
            $result = WordpressComment::create([
                'comment_content' => $request->comment,
                'user_id' => $user->ID,
                'comment_post_ID' =>  $request->video_id,
                'comment_parent' => (int)$request->parent_id,
                'comment_date' => Carbon::now(),
                'comment_date_gmt' => Carbon::now()
            ]);
			WordpressPost::updateCommentCount($request->video_id);
            return $result;
        }, 5);

        Notify::addNotify([
            'content' => [
                "msg" => "{$user->display_name} comment to your video",
                'video_id' => $request->video_id,
                'comment_id' => $comment->comment_ID
            ],
            "user_id" => WordpressPost::find($request->video_id)->post_author,
            'type' => NotifyEnum::COMMENT_VIDEO_TV['value']
        ]);
        
        return response(['data'=> (new CommentResource($comment)), 'message'=> 'Add comment success']);
    }

    public function updateComment(Request $request, WordpressComment $comment)
    {
		
        if($comment->user_id != $request->user()->ID){
            return response()->json(['message' => 'You can only update your own comment.'], 403);
        }
        $request->validate([
			'comment' => 'required|max:500'
            ]);
 
        $comment->update([
			'comment_content' => $request->comment,
            'comment_date' => Carbon::now(),
            'comment_date_gmt' => Carbon::now()
        ]);
        return response(['data'=>$comment, 'message'=> 'update comment success']);
    }
	
	public function deleteComment(Request $request, WordpressComment $comment)
    {
		
        if($comment->user_id != $request->user()->ID){
            return response()->json(['message' => 'You can only delete your own comment.'], 403);
        }
       		
		DB::transaction(function () use ($comment) {
			
			$video_id = $comment->comment_post_ID;
            $comment->delete();
			WordpressPost::updateCommentCount($video_id,false);

        }, 5);
         
        return response(['message'=> 'Comment is deleted']);
    }
	
	public function getLike(WordpressPost $video)
    {
        //
        $data = WordpressLike::with('user')->where('post_id', '=', $video->ID)->get();
 
        return response(['data'=>$data]);
    } 
	
	public function addLike(Request $request)
    {
        $request->validate([
            'video_id' => 'required|exists:wp_posts,ID'
            ]);
        $user = $request->user();
		if(WordpressLike::where('post_id',$request->video_id)->where('user_id',$user->ID)->count()){
			return response(['message'=> 'Liked']);
		}
		$likeCount = DB::transaction(function () use ($request, $user) {
			
            $result = WordpressLike::create([
                'post_id' => $request->video_id,
                'user_id' => $user->ID,
                'date_time' => Carbon::now()
            ]);
			
			$count = WordpressPost::updateLikeCount($request->video_id);
            return $count;
        }, 5);

        Notify::addNotify([
            'content' => [
                "msg" => "{$user->display_name} upload new video",
                'video_id' => $request->video_id,
                'user_id' => $user->ID
            ],
            "user_id" => WordpressPost::find($request->video_id)->post_author,
            'type' => NotifyEnum::LIKE_VIDEO_TV['value']
        ]);
        return response(['data'=>$likeCount, 'message'=> 'Liked']);
    }
	
	public function unLike(Request $request, WordpressPost $video)
    {
		$user = $request->user();
		$count = DB::transaction(function () use ($video,$user) {
			$video_id = $video->ID;
			WordpressLike::where('post_id',$video->ID)->where('user_id',$user->ID)->delete();
            
			$count = WordpressPost::updateLikeCount($video_id);
            return $count;
        }, 5);
         
        return response(['data' => $count,'message'=> 'Unliked']);
    }
}