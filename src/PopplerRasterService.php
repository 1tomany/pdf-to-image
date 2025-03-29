<?php

namespace OneToMany\PdfToImage;

use OneToMany\PdfToImage\Contract\OutputFormat;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Exception\RasterizationFailedException;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use OneToMany\PdfToImage\Record\RasterizedFile;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;

// use function is_executable;
// use function sys_get_temp_dir;

final readonly class PopplerRasterService implements RasterServiceInterface
{

    private Filesystem $filesystem;

    public function __construct(private string $rasterizerPath = 'pdftoppm')
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * @see OneToMany\PdfToImage\RasterServiceInterface
     */
    public function rasterize(RasterizeFileRequest $request): RasterizedFile
    {
        if ($this->filesystem->exists($this->rasterizerPath)) {
            $rasterizerPath = $this->rasterizerPath;
        } else {
            $rasterizerPath = new ExecutableFinder()->find($this->rasterizerPath);

            if (null === $rasterizerPath) {
                throw new InvalidArgumentException(sprintf('The Poppler binary "%s" could not be found in the PATH.', $this->rasterizerPath));
            }
        }

        // if (!is_executable($rasterizerPath)) {
        //     throw new InvalidArgumentException(sprintf('The Poppler binary "%s" is not executable.', $rasterizerPath));
        // }

        try {
            $imageFormat = $this->resolveImageFormat(...[
                'format' => $request->format,
            ]);

            $process = Process::fromShellCommandline($rasterizerPath . ' -q -singlefile "${:IMAGE_FORMAT}" -r "${:RESOLUTION}" "${:FILE}"');

            $process->mustRun(null, [
                'IMAGE_FORMAT' => $imageFormat,
                'RESOLUTION' => $request->resolution,
                'FILE' => $request->inputPath,
            ]);

            $data = $process->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new RasterizationFailedException(path: $request->inputPath, previous: $e);
        }

        return new RasterizedFile($data);

            /*
            $outputPath = $request->outputPath ?? $this->filesystem->tempnam(
                sys_get_temp_dir(), '__1n__raster_', $request->format->suffix(),
            );

            $this->filesystem->dumpFile(
                $outputPath, $data
            );
            */
        // } catch (\Exception $e) {
        //     $this->safelyCleanupFiles(...[
        //         'path' => $outputPath,
        //     ]);


        // }

        // return new RasterizedFile($outputPath);
    }

    private function resolveImageFormat(OutputFormat $format): string
    {
        $imageFormat = match($format) {
            OutputFormat::Jpg => '-jpeg',
            OutputFormat::Jpeg => '-jpeg',
            OutputFormat::Png => '-png',
            OutputFormat::Tiff => '-tiff',
        };

        return $imageFormat;
    }

    /*
    private function safelyCleanupFiles(?string $path): void
    {
        if (!$path) {
            return;
        }

        try {
            if ($this->filesystem->exists($path)) {
                $this->filesystem->remove($path);
            }
        } catch (IOExceptionInterface $e) { }
    }
    */

}
