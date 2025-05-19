<?php

namespace OneToMany\PdfToImage\Contract;

enum ImageType
{
    case Jpeg;
    case Png;

    public function mimeType(): string
    {
        $mimeType = match ($this) {
            static::Jpeg => 'image/jpeg',
            static::Png => 'image/png',
        };

        return $mimeType;
    }
}
