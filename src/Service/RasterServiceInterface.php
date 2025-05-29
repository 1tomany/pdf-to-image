<?php

namespace OneToMany\PdfToImage\Service;

use OneToMany\PdfToImage\Record\RasterImage;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;

interface RasterServiceInterface
{
    /**
     * @return list<RasterImage>
     */
    public function rasterize(RasterizeFileRequest $request): array;
}
