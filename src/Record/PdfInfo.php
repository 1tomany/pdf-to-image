<?php

namespace OneToMany\PdfToImage\Record;

final readonly class PdfInfo
{
    public function __construct(
        public int $pages = 1,
    ) {
    }
}
