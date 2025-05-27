<?php

namespace OneToMany\PdfToImage\Exception;

use function rtrim;
use function sprintf;

final class RasterizingPdfFailedException extends \RuntimeException implements ExceptionInterface
{
    public function __construct(string $file, int $page, ?string $error = null, ?\Throwable $previous = null, int $code = 0)
    {
        $message = sprintf('An error occurred when attempting to rasterize page %d of file "%s".', $page, $file);

        parent::__construct(null === $error ? $message : sprintf('%s: %s.', rtrim($message, '.'), rtrim($error, '.')), $code, $previous);
    }
}
