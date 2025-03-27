<?php

namespace OneToMany\PdfToImage;

use OneToMany\PdfToImage\Record\RasterizedFile;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;

interface RasterServiceInterface
{

    public function rasterize(RasterizeFileRequest $request): RasterizedFile;

}
