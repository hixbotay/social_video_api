<?php
namespace App\Http\Enums; 

class VideoStatusEnum extends Enum {
    const PUBLIC = ['value'=>1,'display'=>'Public'];
    const DRAFT = ['value'=>0,'display'=>'Draft'];
}