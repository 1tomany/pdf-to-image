<?php

namespace OneToMany\PdfToImage\Request;

use OneToMany\PdfToImage\Contract\OutputFormat;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;

final readonly class RasterizeFileRequest
{

    public function __construct(
        public string $inputPath,
        public OutputFormat $format = OutputFormat::Jpeg,
        public int $resolution = 300,
        public ?string $outputPath = null,
    )
    {
        if (!@is_file($this->inputPath) || !@is_readable($this->inputPath)) {
            throw new InvalidArgumentException(sprintf('The input file "%s" does not exist or is not readable.', $this->inputPath));
        }
    }

}
