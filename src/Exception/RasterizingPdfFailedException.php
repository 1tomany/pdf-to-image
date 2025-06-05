<?php

namespace OneToMany\PdfToImage\Exception;

use function sprintf;

final class RasterizingPdfFailedException extends BinaryProcessFailedException
{
    public function __construct(string $file, int $page, ?string $error = null, ?\Throwable $previous = null)
    {
        parent::__construct(sprintf('Failed to rasterize page %d of file "%s".', $page, $file), $error, $previous);
    }
}
