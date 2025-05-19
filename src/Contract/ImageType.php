<?php

namespace OneToMany\PdfToImage\Contract;

enum ImageType
{
    case Jpeg;
    case Png;
    case Tiff;

    public function mimeType(): string
    {
        $mimeType = match ($this) {
            static::Jpeg => 'image/jpeg',
            static::Png => 'image/png',
            static::Tiff => 'image/tiff',
        };

        return $mimeType;
    }
}
