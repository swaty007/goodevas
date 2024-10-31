<?php

namespace Brackets\CraftablePro\Media\Traits;

trait ImageMimeTypesTrait
{
    public function getAllImageMimeTypes(): string
    {
        return "image/png,image/jpeg,image/gif,image/jpg,image/svg+xml,image/webp,image/bmp";
    }

    public function getAllImageFileTypes(): string
    {
        return ".jpg,.jpeg,.png,.gif,.tiff,.bmp,.svg,.webp,.bmp";
    }

    public function getAllDocumentsTypes(): string
    {
        return ".pdf,.doc,.docx,.xls,.xlsx,.csv,.txt,.rtf,.zip";
    }
}
