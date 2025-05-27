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

use function vsprintf;

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

        $shellCommandLine = vsprintf('%s -q -f "%s" "%s" -r "%s" "%s"', [
            $this->binary, '${:PAGE}', '${:TYPE}', '${:RES}', '${:PATH}',
        ]);

        try {
            $process = Process::fromShellCommandline(...[
                'command' => $shellCommandLine,
            ]);
        } catch (ProcessExceptionInterface $e) {
            throw new RuntimeException('The pdftoppm binary could not be executed because PHP was not compiled with the "proc_open" function.', $e);
        }

        try {
            $process->mustRun(null, [
                'PAGE' => $request->pageNumber,
                'TYPE' => $imageTypeArgument,
                'RES' => $request->resolution,
                'PATH' => $request->filePath,
            ]);

            $image = $process->getOutput();
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
