<?php

namespace OneToMany\PdfToImage\Contract;

enum OutputFormat: string
{

    case Jpg = 'jpg';
    case Jpeg = 'jpeg';
    case Png = 'png';
    case Tiff = 'tiff';

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
