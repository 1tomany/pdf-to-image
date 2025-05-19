<?php

namespace OneToMany\PdfToImage\Exception;

use function rtrim;
use function sprintf;

final class RasterizationFailedException extends \RuntimeException implements ExceptionInterface
{
    public function __construct(string $file, string $error, ?\Throwable $previous = null, int $code = 0)
    {
        parent::__construct(sprintf('An error occurred when attempting to rasterize the file "%s": %s.', $file, rtrim($error, '.')), $code, $previous);
    }
}
