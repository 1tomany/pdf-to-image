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

use function sys_get_temp_dir;

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

        try {
            $imageType = match ($request->type) {
                ImageType::Jpeg => '-jpeg',
                ImageType::Png => '-png',
            };

            $pageNumber = $request->firstPage;

            do {
                $process = new Process([
                    $this->binary,
                    '-q',
                    $imageType,
                    '-f',
                    $pageNumber,
                    '-l',
                    $pageNumber,
                    '-r',
                    $request->resolution,
                    $request->filePath,
                ]);

                $image = $process->mustRun()->getOutput();

                $filePath = $this->filesystem->tempnam($request->outputDirectory ?? sys_get_temp_dir(), '__1n__pdf_page_', $request->type->fileSuffix());

                $this->filesystem->dumpFile($filePath, $image);

                $rasterImages[] = new RasterImage($filePath, $request->type);

                ++$pageNumber;
            } while ($pageNumber <= $request->lastPage);
        } catch (ProcessExceptionInterface $e) {
            throw new RasterizingPdfFailedException($request->filePath, $request->firstPage, isset($process) ? $process->getErrorOutput() : null, $e);
        }

        return $rasterImages;
    }
}
