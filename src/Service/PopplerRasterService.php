<?php

namespace OneToMany\PdfToImage\Service;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\RasterizingPdfFailedException;
use OneToMany\PdfToImage\Helper\BinaryFinder;
use OneToMany\PdfToImage\Record\RasterData;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\Process;

final readonly class PopplerRasterService implements RasterServiceInterface
{
    private string $binary;

    public function __construct(string $binary = 'pdftoppm')
    {
        $this->binary = BinaryFinder::find($binary);
    }

    /**
     * @see OneToMany\PdfToImage\RasterServiceInterface
     */
    public function rasterize(RasterizeFileRequest $request): RasterData
    {
        try {
            $imageTypeArg = match ($request->type) {
                ImageType::Jpeg => '-jpeg',
                ImageType::Png => '-png',
            };

            $process = new Process([
                $this->binary,
                '-q',
                $imageTypeArg,
                '-f',
                $request->page,
                '-r',
                $request->resolution,
                $request->filePath,
            ]);

            $image = $process->mustRun()->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new RasterizingPdfFailedException($request->filePath, $request->page, isset($process) ? $process->getErrorOutput() : null, $e);
        }

        return new RasterData($request->type, $image);
    }
}
