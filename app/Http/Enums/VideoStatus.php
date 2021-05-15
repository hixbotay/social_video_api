<?php
namespace App\Http\Enums; 

class VideoStatusEnum extends Enum {
    static $PUBLIC = ['value'=>1,'display'=>'Public'];
    static $DRAFT = ['value'=>0,'display'=>'Draft'];
}