<?php

namespace OneToMany\PdfToImage\Helper;

use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Record\PdfData;
use RuntimeException;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\Process;

readonly class PdfInfo
{
    private string $binary;

    public function __construct(string $binary = 'pdfinfo')
    {
        $this->binary = BinaryFinder::find($binary);
    }

    public function read(string $filePath): PdfData
    {
        try {
            $process = new Process([$this->binary, $filePath])->mustRun();
        } catch (ProcessExceptionInterface $e) {
            throw new \RuntimeException('no good proc_open()', 500, $e);
        }

        try {
            $info = $process->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new \RuntimeException('no good output', 500, $e);
            //throw new RasterizationFailedException($request->filePath, $process->getErrorOutput(), $e);
        }

        $pages = 1;

        foreach (\explode("\n", $info) as $bit) {
            if (\str_starts_with($bit, 'Pages:')) {
                $bit = \trim(\substr($bit, 6));

                if (\is_numeric($bit)) {
                    $pages = \intval($bit);
                }
            }
        }

        return new PdfData($pages);
    }
}
