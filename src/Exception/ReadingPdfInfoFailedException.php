<?php

namespace OneToMany\PdfToImage\Exception;

use function sprintf;

final class ReadingPdfInfoFailedException extends BinaryProcessFailedException
{
    public function __construct(string $file, ?string $error = null, ?\Throwable $previous = null, int $code = 0)
    {
        parent::__construct(sprintf('An error occurred when attempting to read the info for file "%s".', $file), $error, $previous, $code);
    }
}
