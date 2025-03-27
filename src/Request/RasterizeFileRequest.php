<?php

namespace OneToMany\PdfToImage\Request;

use OneToMany\PdfToImage\Contract\OutputFormat;

final readonly class RasterizeFileRequest
{

    public function __construct(
        public string $inputPath,
        public OutputFormat $format = OutputFormat::Jpeg,
        public int $resolution = 300,
        public ?string $outputPath = null,
    )
    {
    }

}
