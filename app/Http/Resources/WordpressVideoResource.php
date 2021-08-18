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
            'id' => $this->ID,
            'user' => $this->user,
            'title' => $this->post_title,
            'description' => $this->post_content,
            'path' => $this->path,
			//'status' => $this->status,
			'thumbnail_path' => $this->thumbnail_path,
			'view' => $this->view,
			'number_comment' => $this->number_comment,
			'number_like' => $this->number_like,
			'comment' => [],
			'created_at' => $this->post_date,
			'share_url' => config('app.web_url').'share-video/?type=tv&video_id='.$this->ID,
			'web_url' => config('app.web_url').'?p='.$this->ID
			
        ];
    }
}