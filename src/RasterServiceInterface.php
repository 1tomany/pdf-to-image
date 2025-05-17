<?php

namespace OneToMany\PdfToImage;

use OneToMany\PdfToImage\Record\RasterData;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;

interface RasterServiceInterface
{
    public function rasterize(RasterizeFileRequest $request): RasterData;
}
