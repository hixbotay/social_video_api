<?php
namespace App\Http\Enums; 

class NotifyEnum extends Enum {
    const SYSTEM = ['value'=>1,'display'=>'System'];
    const FRIEND_REQUEST = ['value'=>2,'display'=>'Add friend request'];
    const ACCEPT_FRIEND = ['value'=>3,'display'=>'Accept friend'];
    const DECLINE_FRIEND = ['value'=>6,'display'=>'Decline friend'];
    const FOLLOW_ME = ['value'=>5,'display'=>'Follow me'];
    const NEW_VIDEO = ['value'=>4,'display'=>'New video'];
    const NEW_VIDEO_TV = ['value'=>9,'display'=>'New video TV'];
    const LIKE_VIDEO = ['value'=>7,'display'=>'Like video'];
    const LIKE_VIDEO_TV = ['value'=>10,'display'=>'Like video TV'];
    const COMMENT_VIDEO = ['value'=>8,'display'=>'Comment video'];
    const COMMENT_VIDEO_TV = ['value'=>11,'display'=>'Comment video TV'];
}