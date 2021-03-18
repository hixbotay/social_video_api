<?php
 
namespace App\Http\Resources;
 
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
 
class VideoResource extends JsonResource
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
            'path' => Storage::url($this->path),
			'status' => $this->status,
			'thumbnail_path' => $this->thumbnail_path,
			'view' => $this->view,
			'created_at' => $this->created_at,
			
        ];
    }
}