<?php

namespace OneToMany\PdfToImage\Exception;

use function sprintf;

final class ReadingPdfInfoFailedException extends \RuntimeException implements ExceptionInterface
{
    public function __construct(string $file, ?\Throwable $previous = null, int $code = 0)
    {
        parent::__construct(sprintf('An error occurred when attempting to read the info for the file "%s".', $file), $code, $previous);
    }
}
