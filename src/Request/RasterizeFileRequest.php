<?php

namespace OneToMany\PdfToImage\Request;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;

use function is_file;
use function is_readable;
use function max;
use function min;
use function sprintf;
use function trim;

final readonly class RasterizeFileRequest
{
    public string $filePath;
    public int $firstPage;
    public int $finalPage;
    public ImageType $format;
    public int $resolution;

    public const int MIN_RESOLUTION = 72;
    public const int MAX_RESOLUTION = 300;

    public function __construct(
        string $filePath,
        ?ImageType $format = null,
        int $firstPage = 1,
        int $finalPage = 1,
        int $resolution = 150,
    ) {
        // Validate the File Exists
        $filePath = trim($filePath);

        if (empty($filePath)) {
            throw new InvalidArgumentException('The input file path can not be empty.');
        }

        if (!is_file($filePath) || !is_readable($filePath)) {
            throw new InvalidArgumentException(sprintf('The input file "%s" does not exist or is not readable.', $filePath));
        }

        // Clamp First and Final Page Numbers
        $firstPage = max(1, $firstPage);
        $finalPage = max(1, $finalPage);

        if ($firstPage > $finalPage) {
            throw new InvalidArgumentException('The first page number must be less than or equal to the final page number.');
        }

        $this->filePath = $filePath;
        $this->firstPage = $firstPage;
        $this->finalPage = $finalPage;

        // Resolve the Output Image Format
        $this->format = $format ?? ImageType::Jpeg;

        // Clamp the Output Resolution
        $this->resolution = max(self::MIN_RESOLUTION, min(self::MAX_RESOLUTION, $resolution));
    }
}
