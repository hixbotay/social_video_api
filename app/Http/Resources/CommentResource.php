<?php
 
namespace App\Http\Resources;
 
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

use function Psy\debug;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $isWp = $this->getTable() != 'wp_comments';
        return [
            'id' => $isWp ? $this->id : $this->comment_ID,
            'user' => $isWp ? $this->user : $this->user,
            'video_id' => $isWp ? $this->video_id : $this->comment_post_ID,
            'parent_id' => $isWp ? $this->parent_id : $this->comment_parent,
            'comment' => $isWp ? $this->comment : $this->comment_content,
            'created_at' => $isWp ? $this->created_at : $this->comment_date,
            'updated_at' => $isWp ? $this->updated_at : $this->comment_date_gmt,
			
        ];
    }
}