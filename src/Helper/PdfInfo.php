<?php

namespace OneToMany\PdfToImage\Helper;

use OneToMany\PdfToImage\Exception\ReadingInfoFailedException;
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

    public function read(string $file): PdfData
    {
        try {
            $process = new Process([
                $this->binary, $file,
            ]);

            $info = $process->mustRun()->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new ReadingInfoFailedException($e, isset($process) ? $process->getErrorOutput() : null, $e);
        }

        foreach (explode("\n", $info) as $infoBit) {
            if (\str_contains($infoBit, ':')) {
                $bits = explode(':', $infoBit);

                if (0 === strcmp('Pages', $bits[0])) {
                    $pages = intval(trim($bits[1]));
                }
            }
        }

        return new PdfData($pages ?? 1);
    }
}
