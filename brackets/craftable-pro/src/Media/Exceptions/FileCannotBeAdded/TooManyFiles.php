<?php

namespace Brackets\CraftablePro\Media\Exceptions\FileCannotBeAdded;

use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class TooManyFiles extends FileCannotBeAdded
{
    public static function create(?int $maxFileCount = null, ?string $collectionName = null): TooManyFiles
    {
        return new static(___(
            'craftable-pro',
            'Max file count in :collectionName is :maxFileCount',
            ['collectionName' => $collectionName, 'maxFileCount' => $maxFileCount]
        ));
    }
}
