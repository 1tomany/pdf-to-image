<?php

namespace OneToMany\PdfToImage\Record;

use OneToMany\DataUri\SmartFile;
use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\RuntimeException;

use function base64_encode;
use function class_exists;
use function file_exists;
use function sprintf;
use function unlink;

final readonly class RasterImage implements \Stringable
{
    public function __construct(
        public string $filePath,
        public ImageType $type,
    ) {
    }

    public function __destruct()
    {
        if (file_exists($this->filePath)) {
            @unlink($this->filePath);
        }
    }

    public function __toString(): string
    {
        return $this->filePath;
    }

    public function toDataUri(): string
    {
        return sprintf('data:%s;base64,%s', $this->type->contentType(), base64_encode($this->filePath));
    }

    public function toSmartFile(): SmartFile // @phpstan-ignore-line
    {
        if (!class_exists(SmartFile::class)) {
            throw new RuntimeException('The raster data can not be converted to a SmartFile because the library is not installed. Try running "composer require 1tomany/data-uri".');
        }

        return \OneToMany\DataUri\parse_data($this->filePath); // @phpstan-ignore-line
    }
}
