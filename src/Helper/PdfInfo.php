<?php

namespace OneToMany\PdfToImage\Helper;

use OneToMany\PdfToImage\Record\PdfData;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\Process;

use function count;
use function explode;
use function intval;
use function strcmp;
use function trim;

use const PHP_EOL;

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
            $pdfInfo = $process->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new \RuntimeException('no good output', 500, $e);
            // throw new RasterizationFailedException($request->filePath, $process->getErrorOutput(), $e);
        }

        $pageCount = 1;

        foreach (explode(PHP_EOL, $pdfInfo) as $infoBit) {
            $bits = explode(':', $infoBit, 2);

            if (2 !== count($bits)) {
                continue;
            }

            if (0 === strcmp('Pages', $bits[0])) {
                $pageCount = intval(trim($bits[1]));
            }
        }

        return new PdfData($pageCount);
    }
}
