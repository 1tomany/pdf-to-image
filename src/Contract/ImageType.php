<?php

namespace OneToMany\PdfToImage\Contract;

enum ImageType
{
    case Jpeg;
    case Png;

    public function fileSuffix(): string
    {
        $fileSuffix = match ($this) {
            self::Jpeg => '.jpeg',
            self::Png => '.png',
        };

        return $fileSuffix;
    }

    public function contentType(): string
    {
        $contentType = match ($this) {
            self::Jpeg => 'image/jpeg',
            self::Png => 'image/png',
        };

        return $contentType;
    }
}
