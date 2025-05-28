<?php

namespace OneToMany\PdfToImage\Contract;

enum ImageType
{
    case Jpeg;
    case Png;

    public function contentType(): string
    {
        $contentType = match ($this) {
            static::Jpeg => 'image/jpeg',
            static::Png => 'image/png',
        };

        return $contentType;
    }
}
