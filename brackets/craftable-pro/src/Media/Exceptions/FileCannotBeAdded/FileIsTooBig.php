<?php

namespace Brackets\CraftablePro\Media\Exceptions\FileCannotBeAdded;

use Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded;

class FileIsTooBig extends FileCannotBeAdded
{
    public static function create(string $file, float $maxSize, string $collectionName): FileIsTooBig
    {
        $actualFileSize = filesize($file);

        return new static(___(
            'craftable-pro',
            'File size is :actualFileSize, while max size of file in :collectionName is :maxSize',
            ['actualFileSize' => $actualFileSize, 'collectionName' => $collectionName, 'maxSize' => $maxSize]
        ));
    }
}
