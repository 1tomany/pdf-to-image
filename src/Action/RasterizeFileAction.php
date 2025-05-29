<?php

namespace OneToMany\PdfToImage\Action;

use OneToMany\PdfToImage\Record\RasterImage;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use OneToMany\PdfToImage\Service\PdfInfoService;
use OneToMany\PdfToImage\Service\RasterServiceInterface;

final readonly class RasterizeFileAction
{
    private PdfInfoService $pdfInfoService;

    public function __construct(private RasterServiceInterface $rasterService)
    {
        $this->pdfInfoService = new PdfInfoService();
    }

    /**
     * @return list<RasterImage>
     */
    public function act(RasterizeFileRequest $request): array
    {
        // Validate the Request
        $pdfInfo = $this->pdfInfoService->read(...[
            'filePath' => $request->filePath,
        ]);

        if ($request->finalPage > $pdfInfo->pageCount) {
            throw new \RuntimeException('final page too large');
        }

        return $this->rasterService->rasterize($request);
    }
}
