<?php

namespace OneToMany\PdfToImage\Service;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\RasterizingPdfFailedException;
use OneToMany\PdfToImage\Helper\BinaryFinder;
use OneToMany\PdfToImage\Record\RasterImage;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\Process;

final readonly class PopplerRasterService implements RasterServiceInterface
{
    private Filesystem $filesystem;
    private string $binary;

    public function __construct(string $binary = 'pdftoppm')
    {
        $this->filesystem = new Filesystem();
        $this->binary = BinaryFinder::find($binary);
    }

    /**
     * @see OneToMany\PdfToImage\RasterServiceInterface
     */
    public function rasterize(RasterizeFileRequest $request): array
    {
        $rasterImages = [];

        $format = match ($request->format) {
            ImageType::Jpeg => '-jpeg',
            ImageType::Png => '-png',
        };

        // $process = Process::fromShellCommandline(\sprintf('%s -q "${:FORMAT}" -f "${:PAGE}" -l "${:PAGE}" -r "${:DPI}" "${:FILE}"', $this->binary), null, [
        //     'FORMAT' => $format,
        //     'DPI' => $request->resolution,
        //     'FILE' => $request->filePath,
        // ]);

        /*
        for ($page = $request->firstPage; $page <= $request->finalPage; ++$page) {
            try {
                $process = new Process([
                    $this->binary,
                    '-q',
                    $format,
                    '-f',
                    $page,
                    '-l',
                    $page,
                    '-r',
                    $request->resolution,
                    $request->filePath,
                ]);

                $image = $process->mustRun()->getOutput();
            } catch (ProcessExceptionInterface $e) {
            }

            // $image = $process->mustRun()->getOutput();
        }
        */

        // $this->filesystem->mkdir($request->outputDirectory);

        $pageNumber = $request->firstPage;

        do {
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

            $image = $process->mustRun()->getOutput();

            $filePath = $this->filesystem->tempnam(
                $request->outputDirectory,
                sprintf('page-%d', $pageNumber),
                $request->format->fileSuffix()
            );

            $this->filesystem->dumpFile($filePath, $image);

            $rasterImages[] = new RasterImage(
                $filePath, $pageNumber, $request->format
            );

            ++$pageNumber;
        } while ($pageNumber <= $request->finalPage);
        // } catch (ProcessExceptionInterface $e) {
        //     throw new RasterizingPdfFailedException($request->filePath, $request->firstPage, isset($process) ? $process->getErrorOutput() : null, $e);
        // }

        return $rasterImages;
    }
}
