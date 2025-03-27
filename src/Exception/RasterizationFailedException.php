<?php

namespace OneToMany\PdfToImage\Exception;

final class RasterizationFailedException extends \RuntimeException implements ExceptionInterface
{

    public function __construct(string $path, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('An error occurred when attempting to rasterize the file "%s".', $path), $code, $previous);
    }

}
