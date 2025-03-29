<?php

namespace OneToMany\PdfToImage\Record;

final readonly class RasterizedFile implements \Stringable
{

    public function __construct(public string $data)
    {
    }

    public function __toString(): string
    {
        return $this->data;
    }
}
