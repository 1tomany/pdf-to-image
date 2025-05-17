<?php

namespace OneToMany\PdfToImage\Action;

use OneToMany\PdfToImage\RasterServiceInterface;
use OneToMany\PdfToImage\Record\RasterData;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;

final readonly class RasterizeFileAction
{
    public function __construct(private RasterServiceInterface $rasterService)
    {
    }

    public function act(RasterizeFileRequest $request): RasterData
    {
        return $this->rasterService->rasterize($request);
    }
}
