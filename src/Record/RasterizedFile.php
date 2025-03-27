<?php

namespace OneToMany\PdfToImage\Record;

final readonly class RasterizedFile implements \Stringable
{

    public function __construct(public string $path)
    {
    }

    public function __toString(): string
    {
        return $this->path;
    }
}
