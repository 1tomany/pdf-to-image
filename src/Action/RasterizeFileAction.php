<?php

namespace OneToMany\PdfToImage\Action;

use OneToMany\PdfToImage\Record\RasterData;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use OneToMany\PdfToImage\Service\RasterServiceInterface;

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
