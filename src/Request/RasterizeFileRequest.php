<?php

namespace OneToMany\PdfToImage\Request;

use OneToMany\PdfToImage\Exception\InvalidArgumentException;

use function is_file;
use function is_readable;

final readonly class RasterizeFileRequest
{
    public function __construct(
        public string $filePath,
        public int $resolution = 300,
    ) {
        if (!is_file($this->filePath) || !is_readable($this->filePath)) {
            throw new InvalidArgumentException(sprintf('The input file "%s" does not exist or is not readable.', $this->filePath));
        }
    }
}
