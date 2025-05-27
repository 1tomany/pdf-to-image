<?php

namespace OneToMany\PdfToImage\Exception;

use function explode;
use function rtrim;
use function sprintf;
use function trim;

class BinaryProcessFailedException extends RuntimeException
{
    public function __construct(string $message, ?string $error = null, ?\Throwable $previous = null)
    {
        $error = trim(explode("\n", $error ?? '')[0]) ?: null;

        parent::__construct(null === $error ? $message : sprintf('%s: %s.', rtrim($message, '.'), rtrim($error, '.')), previous: $previous);
    }
}
