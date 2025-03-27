<?php

namespace OneToMany\PdfToImage\Exception;

final class InvalidRasterServiceException extends \InvalidArgumentException implements ExceptionInterface
{

    public function __construct(string $service, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('The raster service "%s" is invalid.', $service), $code, $previous);
    }

}
