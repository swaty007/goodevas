<?php

namespace Brackets\CraftablePro\Media;

use Spatie\MediaLibrary\MediaCollections\MediaCollection as ParentMediaCollection;

class MediaCollection extends ParentMediaCollection
{
    public float $maxFileSize;

    protected string $viewPermission = '';

    protected string $uploadPermission = '';

    /**
     * MediaCollection constructor.
     */
    public function __construct(string $name)
    {
        parent::__construct($name);

        $this->diskName = config('craftable-pro.default_media_disk_name');
        $this->maxFileSize = config('media-library.max_file_size');
    }

    /**
     * Set the file size limit
     *
     *
     * @return $this
     */
    public function maxFileSize($maxFileSize): self
    {
        $this->maxFileSize = $maxFileSize;

        return $this;
    }

    /**
     * Set the ability (Gate) which is required to view the medium
     *
     * In most cases you would want to call private() to use default private disk.
     *
     * Otherwise, you may use other private disk for your own. Just be sure, your file is not accessible
     *
     *
     * @return $this
     */
    public function canView($viewPermission): self
    {
        $this->viewPermission = $viewPermission;

        return $this;
    }

    /**
     * Set the ability (Gate) which is required to upload & attach new files to the model
     *
     *
     * @return $this
     */
    public function canUpload($uploadPermission): self
    {
        $this->uploadPermission = $uploadPermission;

        return $this;
    }

    public function isImage(): bool
    {
        return collect($this->acceptsMimeTypes)->reject(static function ($mimeType) {
            return strpos($mimeType, 'image') === 0;
        })->count() === 0;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDisk(): ?string
    {
        return $this->diskName;
    }

    public function getViewPermission(): ?string
    {
        return $this->viewPermission;
    }

    public function getUploadPermission(): ?string
    {
        return $this->uploadPermission;
    }
}
