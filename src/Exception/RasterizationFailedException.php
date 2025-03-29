<?php

namespace OneToMany\PdfToImage\Exception;

use function sprintf;

final class RasterizationFailedException extends \RuntimeException implements ExceptionInterface
{

    public function __construct(string $file, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('An error occurred when attempting to rasterize the file "%s".', $file), 0, $previous);
    }

}
