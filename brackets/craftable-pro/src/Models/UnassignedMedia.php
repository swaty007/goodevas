<?php

namespace Brackets\CraftablePro\Models;

use Brackets\CraftablePro\Media\AutoProcessMediaTrait;
use Brackets\CraftablePro\Media\InteractsWithMedia;
use Brackets\CraftablePro\Media\ProcessMediaTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class UnassignedMedia extends Model implements HasMedia
{
    use AutoProcessMediaTrait;
    use InteractsWithMedia;
    use ProcessMediaTrait;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->singleFile();
    }
}
