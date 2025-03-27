<?php

namespace OneToMany\PdfToImage\Request;

use OneToMany\PdfToImage\Contract\OutputFormat;

final class RasterizeFileRequest
{

    public function __construct(
        public readonly string $inputPath,
        public readonly OutputFormat $format = OutputFormat::Jpeg,
        public readonly int $resolution = 300,
        public ?string $outputPath = null,
    )
    {
    }

}
