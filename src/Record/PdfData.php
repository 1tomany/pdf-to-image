<?php

namespace OneToMany\PdfToImage\Record;

final readonly class PdfData
{
    public function __construct(
        public int $pages = 1,
    ) {
    }
}
