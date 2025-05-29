<?php

namespace OneToMany\PdfToImage\Request;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;

use function bin2hex;
use function is_dir;
use function is_file;
use function is_readable;
use function is_writable;
use function max;
use function random_bytes;
use function sprintf;
use function sys_get_temp_dir;
use function trim;

final readonly class RasterizeFileRequest
{
    public string $filePath;
    public int $firstPage;
    public int $finalPage;
    public ImageType $format;
    public int $resolution;
    public string $outputDirectory;

    private const int MIN_RESOLUTION = 48;
    private const int MAX_RESOLUTION = 300;

    public function __construct(
        string $filePath,
        int $firstPage = 1,
        int $finalPage = 1,
        ?ImageType $format = null,
        int $resolution = 150,
        ?string $outputDirectory = null,
    ) {
        // Validate the PDF File Exists
        $this->filePath = trim($filePath);

        if (!is_file($this->filePath) || !is_readable($this->filePath)) {
            throw new InvalidArgumentException(sprintf('The input file "%s" does not exist or is not readable.', $this->filePath));
        }

        // Clamp First and Final Page Numbers
        $this->firstPage = max(1, $firstPage);
        $this->finalPage = max(1, $finalPage);

        if ($this->firstPage > $this->finalPage) {
            throw new InvalidArgumentException('The first page number must be less than or equal to the final page number.');
        }

        // Resolve the Output Image Format
        $this->format = $format ?? ImageType::Jpeg;

        // Clamp the Output Resolution
        $this->resolution = \max(self::MIN_RESOLUTION, \min(self::MAX_RESOLUTION, $resolution));

        if (null !== $outputDirectory && (!is_dir($outputDirectory) || !is_writable($outputDirectory))) {
            throw new InvalidArgumentException(sprintf('The output directory "%s" does not exist or is not writable.', $outputDirectory));
        }

        $this->outputDirectory = $outputDirectory ?? sprintf('%s/__1n__pdf_pages_%s', sys_get_temp_dir(), bin2hex(random_bytes(4)));
    }
}
