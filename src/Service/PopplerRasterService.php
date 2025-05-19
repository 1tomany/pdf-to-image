<?php

namespace OneToMany\PdfToImage\Service;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Exception\RasterizationFailedException;
use OneToMany\PdfToImage\Exception\RuntimeException;
use OneToMany\PdfToImage\Record\RasterData;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

use function is_executable;
use function sprintf;

final readonly class PopplerRasterService implements RasterServiceInterface
{
    private ExecutableFinder $finder;

    public function __construct(private string $pdftoppmBinary = 'pdftoppm')
    {
        $this->finder = new ExecutableFinder();
    }

    /**
     * @see OneToMany\PdfToImage\RasterServiceInterface
     */
    public function rasterize(RasterizeFileRequest $request): RasterData
    {
        // Resolve the `pdftoppm` Binary Path
        $pdfToPpmBinary = $this->findBinaryPath(...[
            'popplerBinary' => $this->pdftoppmBinary,
        ]);

        // Construct the `pdftoppm` Command
        $imageTypeArgument = $this->resolveImageType(...[
            'imageType' => $request->imageType,
        ]);

        $shellCommandLine = vsprintf('%s -q -f "%s" "%s" -r "%s" "%s"', [
            $pdfToPpmBinary, '${:PAGE}', '${:TYPE}', '${:RES}', '${:PATH}',
        ]);

        try {
            $process = Process::fromShellCommandline(...[
                'command' => $shellCommandLine,
            ]);
        } catch (ProcessExceptionInterface $e) {
            throw new RuntimeException('The Poppler binary "%s" could not be executed because PHP was not compiled with the "proc_open()" function.', $e);
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

    private function resolveImageType(ImageType $imageType): string
    {
        $imageType = match ($imageType) {
            ImageType::Jpeg => '-jpeg',
            ImageType::Png => '-png',
        };

        return $imageType;
    }
}
