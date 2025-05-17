<?php

namespace OneToMany\PdfToImage;

use OneToMany\PdfToImage\Record\RasterData;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;

final readonly class MockRasterService implements RasterServiceInterface
{
    /**
     * @see OneToMany\PdfToImage\RasterServiceInterface
     */
    public function rasterize(RasterizeFileRequest $request): RasterData
    {
        throw new \RuntimeException('Not implemented!');
    }
}
