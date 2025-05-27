<?php

namespace OneToMany\PdfToImage\Exception;

use function rtrim;
use function sprintf;

final class RasterizationFailedException extends \RuntimeException implements ExceptionInterface
{
    public function __construct(string $file, ?string $error = null, ?\Throwable $previous = null, int $code = 0)
    {
        $message = sprintf('An error occurred when attempting to rasterize the file "%s".', $file);

        parent::__construct(null === $error ? $message : sprintf('%s: %s.', rtrim($message, '.'), rtrim($error, '.')), $code, $previous);
    }
}
