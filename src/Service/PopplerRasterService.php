<?php

namespace OneToMany\PdfToImage\Service;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Exception\RasterizationFailedException;
use OneToMany\PdfToImage\Record\RasterData;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

use function is_executable;
use function sprintf;
use function vsprintf;

final readonly class PopplerRasterService implements RasterServiceInterface
{
    private ExecutableFinder $finder;

    public function __construct(
        // private string $pdfinfoBinary = 'pdfinfo',
        private string $pdftoppmBinary = 'pdftoppm',
    ) {
        $this->finder = new ExecutableFinder();
    }

    /**
     * @see OneToMany\PdfToImage\RasterServiceInterface
     */
    public function rasterize(RasterizeFileRequest $request): RasterData
    {
        $pdfToPpmBinary = $this->findBinaryPath(...[
            'popplerBinary' => $this->pdftoppmBinary,
        ]);

        try {
            // Construct the pdftoppm Conversion Command
            $imageType = match ($request->imageType) {
                ImageType::Jpeg => '-jpeg',
                ImageType::Png => '-png',
                ImageType::Tiff => '-tiff',
            };

            $command = \sprintf('%s -q -f "${:PAGENUMBER}" -l "${:PAGENUMBER}" "${:IMAGETYPE}" -r "${:RESOLUTION}" "${:FILEPATH}"', $pdfToPpmBinary);

            $process = Process::fromShellCommandline($command)->mustRun(null, [
                'PAGENUMBER' => $request->pageNumber,
                'IMAGETYPE' => $imageType,
                'RESOLUTION' => $request->resolution,
                'FILEPATH' => $request->filePath,
            ]);

            $data = $process->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new RasterizationFailedException($request->filePath, $e);
        }

        return new RasterData($request->imageType->mimeType(), $data);
    }

    private function findBinaryPath(string $popplerBinary): string
    {
        if (is_executable($popplerBinary)) {
            return $popplerBinary;
        }

        if (null === $binary = $this->finder->find($popplerBinary)) {
            throw new InvalidArgumentException(sprintf('The Poppler binary "%s" could not be found.', $popplerBinary));
        }

        return $binary;
    }
}
