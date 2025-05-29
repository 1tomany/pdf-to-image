<?php

namespace OneToMany\PdfToImage\Request;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;

use function is_file;
use function is_readable;
use function sprintf;

final readonly class RasterizeFileRequest
{
    private const int MIN_RESOLUTION = 48;
    private const int MAX_RESOLUTION = 300;

    public function __construct(
        public string $filePath,
        public int $firstPage = 1,
        public int $lastPage = 1,
        public ImageType $type = ImageType::Jpeg,
        public int $resolution = 150,
        public ?string $outputDirectory = null,
    ) {
        if (!is_file($this->filePath) || !is_readable($this->filePath)) {
            throw new InvalidArgumentException(sprintf('The input file "%s" does not exist or is not readable.', $this->filePath));
        }

        if ($this->firstPage < 1 || $this->firstPage < $this->lastPage) {
            throw new InvalidArgumentException('The first page number must be a positive non-zero integer and greater than or equal to the last pagenumber .');
        }

        if ($this->resolution < self::MIN_RESOLUTION || $this->resolution > self::MAX_RESOLUTION) {
            throw new InvalidArgumentException(sprintf('The resolution must be an integer between %d and %d.', self::MIN_RESOLUTION, self::MAX_RESOLUTION));
        }
    }
}
