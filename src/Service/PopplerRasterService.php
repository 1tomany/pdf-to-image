<?php

namespace OneToMany\PdfToImage\Service;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\RasterizingPdfFailedException;
use OneToMany\PdfToImage\Helper\BinaryFinder;
use OneToMany\PdfToImage\Record\RasterImage;
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
    public function rasterize(RasterizeFileRequest $request): array
    {
        $rasterImages = [];

        $rasterizeCommand = vsprintf('%s -q %s -f "${:PAGE}" -l "${:PAGE}" -r %d "${:FILE}"', [
            $this->binary, $this->getOutputFormat($request->format), $request->resolution,
        ]);

        //var_dump($rasterizeCommand);

        // $format = $this->getOutputFormat(...[
        //     'format' => $request->format,
        // ]);
        $pageNumber = $request->firstPage;

        do {
            $process = Process::fromShellCommandline($rasterizeCommand, null, [
                'FILE' => $request->filePath,
            ]);

            $process->mustRun(null, [
                'PAGE' => $pageNumber,
            ]);

            $bytes = $process->getOutput();

            $rasterImages[] = new RasterImage($bytes, $request->format, $pageNumber);

            ++$pageNumber;
        } while ($pageNumber <= $request->finalPage);

        /*
        for ($pageNumber = $request->firstPage; $pageNumber <= $request->finalPage; ++$pageNumber) {
            try {
                $process = new Process([
                    $this->binary,
                    '-q',
                    $format,
                    '-f',
                    $pageNumber,
                    '-l',
                    $pageNumber,
                    '-r',
                    $request->resolution,
                    $request->filePath,
                ]);

                $process->mustRun();

                $rasterImages[] = new RasterImage(...[
                    'bytes' => $process->getOutput(),
                    'format' => $request->format,
                    'pageNumber' => $pageNumber,
                ]);
            } catch (ProcessExceptionInterface $e) {
                throw new RasterizingPdfFailedException($request->filePath, $pageNumber, isset($process) ? $process->getErrorOutput() : null, $e);
            }
        }
        */

        return $rasterImages;
    }

    private function getOutputFormat(ImageType $format): string
    {
        return match ($format) {
            ImageType::Jpeg => '-jpeg',
            ImageType::Png => '-png',
        };
    }
}
