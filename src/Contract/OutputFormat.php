<?php

namespace OneToMany\PdfToImage\Contract;

enum OutputFormat
{

    case Jpg;
    case Jpeg;
    case Png;
    case Tiff;

    public function mediaType(): string
    {
        $suffix = match($this) {
            static::Jpg => 'image/jpeg',
            static::Jpeg => 'image/jpeg',
            static::Png => 'image/png',
            static::Tiff => 'image/tiff',
        };

        return $suffix;
    }

    public function suffix(): string
    {
        $suffix = match($this) {
            static::Jpg => '.jpeg',
            static::Jpeg => '.jpeg',
            static::Png => '.png',
            static::Tiff => '.tiff',
        };

        return $suffix;
    }

}
