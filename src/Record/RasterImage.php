<?php

namespace OneToMany\PdfToImage\Record;

use OneToMany\DataUri\SmartFile;
use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\RuntimeException;

use function base64_encode;
use function class_exists;
use function sprintf;

final readonly class RasterImage implements \Stringable
{
    public function __construct(
        public string $bytes,
        public ImageType $format,
        public int $pageNumber = 1,
    ) {
    }

    public function __toString(): string
    {
        return $this->bytes;
    }

    public function toDataUri(): string
    {
        return sprintf('data:%s;base64,%s', $this->format->contentType(), base64_encode($this->bytes));
    }

    public function toSmartFile(): SmartFile // @phpstan-ignore-line
    {
        if (!class_exists(SmartFile::class)) {
            throw new RuntimeException('The raster image can not be converted to a SmartFile because the library is not installed. Try running "composer require 1tomany/data-uri".');
        }

        return \OneToMany\DataUri\parse_data($this->toDataUri()); // @phpstan-ignore-line
    }
}
