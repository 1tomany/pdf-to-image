<?php

namespace OneToMany\PdfToImage\Service;

use OneToMany\PdfToImage\Request\RasterizeFileRequest;

final readonly class MockRasterService implements RasterServiceInterface
{
    /**
     * @see OneToMany\PdfToImage\RasterServiceInterface
     */
    public function rasterize(RasterizeFileRequest $request): array
    {
        throw new \RuntimeException('Not implemented!');
    }
}
