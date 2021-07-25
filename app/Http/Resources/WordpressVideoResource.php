<?php
 
namespace App\Http\Resources;
 
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
 
class WordpressVideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => $this->user,
            'title' => $this->title,
            'description' => $this->description,
            'path' => $this->path,
			//'status' => $this->status,
			'thumbnail_path' => $this->thumbnail_path,
			'view' => $this->view,
			'number_comment' => $this->number_comment,
			'number_like' => $this->number_like,
			'comment' => [],
			'created_at' => $this->created_at,
			'share_url' => config('app.web_url').'share-video/?type=tv&video_id='.$this->id,
			'web_url' => config('app.web_url').'?p='.$this->id
			
        ];
    }
}