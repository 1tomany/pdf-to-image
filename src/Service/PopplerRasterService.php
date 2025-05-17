<?php

namespace OneToMany\PdfToImage\Service;

use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Exception\RasterizationFailedException;
use OneToMany\PdfToImage\Record\RasterData;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

use function is_executable;
use function sprintf;

final readonly class PopplerRasterService implements RasterServiceInterface
{
    public function __construct(private string $rasterizerPath = 'pdftoppm')
    {
    }

    /**
     * @see OneToMany\PdfToImage\RasterServiceInterface
     */
    public function rasterize(RasterizeFileRequest $request): RasterData
    {
        if (is_executable($this->rasterizerPath)) {
            $rasterizerPath = $this->rasterizerPath;
        } else {
            $rasterizerPath = new ExecutableFinder()->find($this->rasterizerPath);

            if (null === $rasterizerPath) {
                throw new InvalidArgumentException(sprintf('The Poppler binary "%s" could not be found.', $this->rasterizerPath));
            }
        }

        try {
            // Construct the pdftoppm Conversion Command
            $command = \vsprintf('%s -q -singlefile -jpeg -r "%s" "%s"', [
                $rasterizerPath, '${:RESOLUTION}', '${:FILE_PATH}',
            ]);

            $process = Process::fromShellCommandline($command)->mustRun(null, [
                'RESOLUTION' => $request->resolution,
                'FILE_PATH' => $request->filePath,
            ]);

            $data = $process->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new RasterizationFailedException($request->filePath, $e);
        }

        return new RasterData('image/jpeg', $data);
    }
}
