<?php

namespace OneToMany\PdfToImage\Service;

use OneToMany\PdfToImage\Exception\ReadingPdfMetadataFailedException;
use OneToMany\PdfToImage\Helper\BinaryFinder;
use OneToMany\PdfToImage\Record\PdfInfo;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\Process;

use function explode;
use function intval;
use function str_contains;
use function strcmp;
use function trim;

readonly class PdfInfoService
{
    private string $binary;

    public function __construct(string $binary = 'pdfinfo')
    {
        $this->binary = BinaryFinder::find($binary);
    }

    public function read(string $path): PdfInfo
    {
        try {
            $process = new Process([
                $this->binary, $path,
            ]);

            $info = $process->mustRun()->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new ReadingPdfMetadataFailedException($path, isset($process) ? $process->getErrorOutput() : null, $e);
        }

        foreach (explode("\n", $info) as $infoBit) {
            if (str_contains($infoBit, ':')) {
                $bits = explode(':', $infoBit);

                if (0 === strcmp('Pages', $bits[0])) {
                    $pages = intval(trim($bits[1]));
                }
            }
        }

        return new PdfInfo($pages ?? 1);
    }
}
