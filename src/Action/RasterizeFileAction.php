<?php

namespace OneToMany\PdfToImage\Action;

use OneToMany\PdfToImage\RasterServiceInterface;
use OneToMany\PdfToImage\Record\RasterizedFile;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;

final readonly class RasterizeFileAction
{

    public function __construct(private RasterServiceInterface $rasterService)
    {
    }

    public function act(RasterizeFileRequest $request): RasterizedFile
    {
        return $this->rasterService->rasterize($request);
    }

}
