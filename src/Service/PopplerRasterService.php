<?php

namespace OneToMany\PdfToImage\Service;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\RasterizationFailedException;
use OneToMany\PdfToImage\Exception\RuntimeException;
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
        // Construct the `pdftoppm` Command
        $imageTypeArgument = $this->resolveImageType(...[
            'imageType' => $request->imageType,
        ]);

        try {
            $process = new Process([
                $this->binary,
                '-q',
                '-f',
                $request->pageNumber,
                '-r',
                $request->resolution,
                $imageTypeArgument,
                $request->filePath,
            ]);
        } catch (ProcessExceptionInterface $e) {
            throw new RuntimeException('The pdftoppm binary could not be executed because PHP was not compiled with the "proc_open" function.', $e);
        }

        try {
            $image = $process->mustRun()->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new RasterizationFailedException($request->filePath, $process->getErrorOutput(), $e);
        }

        return new RasterData($request->imageType->mimeType(), $image);
    }

    private function resolveImageType(ImageType $imageType): string
    {
        $imageType = match ($imageType) {
            ImageType::Jpeg => '-jpeg',
            ImageType::Png => '-png',
        };

        return $imageType;
    }
}
