<?php

namespace OneToMany\PdfToImage\Request;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;

use function is_file;
use function is_readable;

final readonly class RasterizeFileRequest
{
    private const int MIN_RESOLUTION = 48;
    private const int MAX_RESOLUTION = 300;

    public function __construct(
        public string $filePath,
        public int $pageNumber = 1,
        public ImageType $imageType = ImageType::Jpeg,
        public int $resolution = 150,
    ) {
        if (!is_file($this->filePath) || !is_readable($this->filePath)) {
            throw new InvalidArgumentException(sprintf('The input file "%s" does not exist or is not readable.', $this->filePath));
        }

        if ($this->pageNumber < 1) {
            throw new InvalidArgumentException('The page number must be an integer greater than 0.');
        }

        if ($this->resolution < self::MIN_RESOLUTION || $this->resolution > self::MAX_RESOLUTION) {
            throw new InvalidArgumentException(sprintf('The resolution must be an integer between %d and %d.', self::MIN_RESOLUTION, self::MAX_RESOLUTION));
        }
    }
}
