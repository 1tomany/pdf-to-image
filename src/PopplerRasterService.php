<?php

namespace OneToMany\PdfToImage;

use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use OneToMany\PdfToImage\Record\RasterizedFile;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

use function is_executable;
use function sys_get_temp_dir;

final readonly class PopplerRasterService implements RasterServiceInterface
{

    private Filesystem $filesystem;

    public function __construct(private string $converterPath = 'pdftoppm')
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * @see OneToMany\PdfToImage\RasterServiceInterface
     */
    public function rasterize(RasterizeFileRequest $request): RasterizedFile
    {
        try {
            if ($this->filesystem->exists($this->converterPath)) {
                $converterPath = $this->converterPath;
            } else {
                $converterPath = new ExecutableFinder()->find($this->converterPath);

                if (null === $converterPath) {
                    throw new \RuntimeException(sprintf('The Poppler binary "%s" could not be found in the PATH.', $this->converterPath));
                }
            }

            if (!@is_executable($converterPath)) {
                throw new \RuntimeException(sprintf('the binary "%s" is not executable', $converterPath));
            }

            if (!$this->filesystem->exists($request->inputPath)) {
                throw new \RuntimeException('no readable file path');
            }

            // $imageFormat = $this->imageFormats[
            //     $this->imageFormat
            // ];

            $process = new Process([
                $converterPath,
                '-q',                 // quiet mode
                '-jpeg',              // image format
                '-f',                 // first page
                '1',                  // page number
                '-singlefile',        // single page
                '-r',                 // resolution
                $request->resolution, // dots per inch
                $request->inputPath,  // path to file
            ]);

            $data = $process->mustRun()->getOutput();

            $request->outputPath ??= $this->filesystem->tempnam(
                sys_get_temp_dir(), '__1n__raster_'
            );

            $this->filesystem->dumpFile(
                $request->outputPath, $data
            );
        } catch (\Exception $e) {
            $this->safelyCleanupFiles(...[
                'path' => $request->outputPath,
            ]);

            throw new \Exception('no good convert');
        }

        return new RasterizedFile($request->outputPath);
    }

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

}
