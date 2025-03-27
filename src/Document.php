<?php

namespace OneToMany\PdfToImage;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class Document
{

    private readonly Filesystem $filesystem;
    private readonly array $imageFormats;

    private string $converter = 'pdftoppm';
    private string $imageFormat = 'jpeg';
    private int $resolution = 300;

    public function __construct()
    {
        $this->filesystem = new Filesystem();

        $this->imageFormats = [
            'jpg' => '-jpeg',
            'jpeg' => '-jpeg',
            'png' => '-png',
            'tiff' => '-tiff',
        ];
    }

    public function setConverter(string $converter): self
    {
        $this->converter = trim($converter);

        return $this;
    }

    public function setImageFormat(string $imageFormat): self
    {
        if (!array_key_exists($imageFormat, $this->imageFormats)) {
            throw new \InvalidArgumentException('no good format');
        }

        $this->imageFormat = $imageFormat;

        return $this;
    }

    public function setResolution(int $resolution): self
    {
        if ($resolution <= 0 || $resolution > 300) {
            throw new \InvalidArgumentException('no good resolution');
        }

        $this->resolution = $resolution;

        return $this;
    }

    public function doStuff(?string $outputFilePath = null): void
    {
        try {
            $binaryPath = new ExecutableFinder()->find($this->converter);

            if (null === $binaryPath) {
                throw new \RuntimeException(sprintf('The "%s" binary could not be found.', $this->converter));
            }

            $imageFormat = $this->imageFormats[
                $this->imageFormat
            ];

            $process = new Process([
                $binaryPath,
                '-q',          // quiet mode
                $imageFormat,  // image format
                '-f',          // first page
                '1',           // page number
                '-singlefile', // single page
                '-r',          // resolution
                $this->resolution, // dots per inch
                $record->path  // path to file
            ]);

            $data = $process->mustRun()->getOutput();

            if (null === $outputFilePath) {
                $outputFilePath = $this->filesystem->tempnam(
                    sys_get_temp_dir(), '__1n__document_'
                );
            }

            $this->filesystem->dumpFile(
                $outputFilePath, $data
            );
        } catch (\Exception $e) {
            throw new \Exception('no good convert');
        } finally {
            $this->safelyCleanupFiles(...[
                'path' => $outputFilePath,
            ]);
        }
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
