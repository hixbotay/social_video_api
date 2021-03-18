<?php
 
namespace App\Http\Controllers\API;
 
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
 
class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $videos = VideoResource::collection(Video::with('user')->latest()->paginate(5));
 
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
			'status' => 'required',
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
        return response(['data'=>$video]);
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
        if($video->user_id != $request->user()->id){
            return response()->json(['message' => 'You can only delete your own video.'], 403);
        }
        Storage::delete($video->path);
        $video->delete();
         
        return response(['message'=> 'Video is deleted']);
    }
}