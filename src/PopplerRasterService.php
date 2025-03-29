<?php

namespace OneToMany\PdfToImage;

use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Exception\RasterizationFailedException;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use OneToMany\PdfToImage\Record\RasterizedFile;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessExceptionInterface;

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
    public function rasterize(RasterizeFileRequest $request): RasterizedFile
    {
        if (is_executable($this->rasterizerPath)) {
            $rasterizerPath = $this->rasterizerPath;
        } else {
            $rasterizerPath = new ExecutableFinder()->find($this->rasterizerPath);

            if (null === $rasterizerPath) {
                throw new InvalidArgumentException(sprintf('The Poppler binary "%s" could not be found in the PATH.', $this->rasterizerPath));
            }
        }

        try {
            // Construct the pdftoppm Conversion Command
            $command = vsprintf('%s -q -singlefile -jpeg -r "%s" "%s"', [
                $rasterizerPath, '${:RESOLUTION}', '${:INPUT_FILE}',
            ]);

            $process = Process::fromShellCommandline($command)->mustRun(null, [
                'RESOLUTION' => $request->resolution,
                'INPUT_FILE' => $request->inputFile,
            ]);

            $data = $process->getOutput();
        } catch (ProcessExceptionInterface $e) {
            throw new RasterizationFailedException($request->inputFile, $e);
        }

        return new RasterizedFile(sprintf('data:image/jpeg;base64,%s', base64_encode($data)));
    }

}
