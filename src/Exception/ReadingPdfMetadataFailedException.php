<?php

namespace OneToMany\PdfToImage\Exception;

use function sprintf;

final class ReadingPdfMetadataFailedException extends BinaryProcessFailedException
{
    public function __construct(string $file, ?string $error = null, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Failed to read the metadata for the file "%s".', $file), $error, $previous);
    }
}
