<?php

namespace OneToMany\PdfToImage\Service;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Helper\BinaryFinder;
use OneToMany\PdfToImage\Record\RasterImage;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use Symfony\Component\Process\Process;

use function vsprintf;

final readonly class PopplerRasterService implements RasterServiceInterface
{
    private string $binary;

    public function __construct(string $binary = 'pdftoppm')
    {
        $this->binary = BinaryFinder::find($binary);
    }

    /**
     * @see OneToMany\PdfToImage\RasterServiceInterface
     */
    public function rasterize(RasterizeFileRequest $request): array
    {
        $rasterImages = [];

        $command = vsprintf('%s -q %s -f "${:PAGE}" -l "${:PAGE}" -r %d "${:FILEPATH}"', [
            $this->binary, $this->getOutputFormat($request->format), $request->resolution,
        ]);

        $process = Process::fromShellCommandline($command, null, [
            'FILEPATH' => $request->filePath,
        ]);

        $page = $request->firstPage;

        do {
            $process->mustRun(null, [
                'PAGE' => $page,
            ]);

            $output = $process->getOutput();

            $rasterImages[] = new RasterImage(
                $output, $request->format, $page
            );

            ++$page;
        } while ($page <= $request->finalPage);

        return $rasterImages;
    }

    private function getOutputFormat(ImageType $format): string
    {
        return match ($format) {
            ImageType::Jpeg => '-jpeg',
            ImageType::Png => '-png',
        };
    }
}
