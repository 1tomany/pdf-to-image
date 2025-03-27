<?php

namespace OneToMany\PdfToImage;

use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use OneToMany\PdfToImage\Record\RasterizedFile;

final readonly class MockRasterService implements RasterServiceInterface
{

    /**
     * @see OneToMany\PdfToImage\RasterServiceInterface
     */
    public function rasterize(RasterizeFileRequest $request): RasterizedFile
    {

    }

}
