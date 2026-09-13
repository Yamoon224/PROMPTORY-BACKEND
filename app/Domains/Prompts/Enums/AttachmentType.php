<?php

namespace App\Domains\Prompts\Enums;

enum AttachmentType: string
{
    case Image = 'image';
    case Video = 'video';
    case Link = 'link';
}
