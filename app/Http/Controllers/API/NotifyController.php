<?php
 
namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Http\Resources\WordpressVideoResource;
use App\Models\Video;
use App\Models\WordpressPost;
use App\Models\VideoComment;
use App\Models\VideoLike;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Enums\VideoStatusEnum;
use App\Models\Notify;

class NotifyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        $notify = Notify::where('user_id', '=', $request->user()->ID);
        if($request->is_read){
            $notify = $notify->isRead();
        }
        $unread = Notify::where('user_id', '=', $request->user()->ID)->unRead()->count();
 
        return response(['data'=>$notify->latest()->paginate(20)->items(),'unread' => $unread]);
    } 

    public function markAllRead(Request $request)
    {
        Notify::where('user_id', $request->user()->ID)->update(['is_read'=>1]); 
        return response(['message'=>'Success','unread' => 0]);
    } 
	
	public function markRead(Request $request, Notify $notify)
    {
		$notify->updateRead();
        $unread = Notify::where('user_id', '=', $request->user()->ID)->unRead()->count();
        return response(['data'=>$notify,'unread' =>$unread]);
    }
	
    public function destroy(Request $request, Notify $notify)
    {
        if($notify->user_id != $request->user()->ID){
            return response()->json(['message' => 'You can only delete your own.'], 403);
        }
        $notify->delete();
         
        return response(['message'=> 'Notify is deleted']);
    }
 
    public function show(Notify $notify)
    {
        return response(['data'=>$notify]);
    }
}