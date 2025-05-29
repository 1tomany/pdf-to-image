<?php

namespace OneToMany\PdfToImage\Tests\Request;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function array_rand;
use function random_int;

use const PHP_INT_MAX;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class RasterizeFileRequestTest extends TestCase
{
    private static string $filePath;

    public static function setUpBeforeClass(): void
    {
        self::$filePath = __DIR__.'/files/label.pdf';
    }

    public function testConstructorRequiresNonEmptyFilePath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The input file path can not be empty.');

        new RasterizeFileRequest('');
    }

    public function testConstructorRequiresFilePathToBeReadable(): void
    {
        $filePath = __DIR__.'/invalid.file.path';
        $this->assertFileDoesNotExist($filePath);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The input file "'.$filePath.'" does not exist or is not readable.');

        new RasterizeFileRequest($filePath);
    }

    public function testConstructorRequiresNonEmptyOutputDirectoryToExist(): void
    {
        $outputDirectory = __DIR__.'/invalid/file-directory';
        $this->assertDirectoryDoesNotExist($outputDirectory);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The output directory "'.$outputDirectory.'" does not exist or is not writable.');

        new RasterizeFileRequest(self::$filePath, outputDirectory: $outputDirectory);
    }

    public function testConstructorClampsFirstPageNumber(): void
    {
        $firstPage = -1 * random_int(0, 10);
        $this->assertLessThan(1, $firstPage);

        $this->assertEquals(1, new RasterizeFileRequest(self::$filePath, firstPage: $firstPage)->firstPage);
    }

    public function testConstructorClampsFinalPageNumber(): void
    {
        $finalPage = -1 * random_int(0, 10);
        $this->assertLessThan(1, $finalPage);

        $this->assertEquals(1, new RasterizeFileRequest(self::$filePath, finalPage: $finalPage)->finalPage);
    }

    public function testConstructorRequiresFirstPageToBeLessThanOrEqualToFinalPage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The first page number must be less than or equal to the final page number.');

        new RasterizeFileRequest(self::$filePath, firstPage: random_int(11, 20), finalPage: random_int(1, 10));
    }

    public function testConstructorResolvesFormatWhenNullFormatProvided(): void
    {
        $this->assertSame(ImageType::Jpeg, new RasterizeFileRequest(self::$filePath, format: null)->format);
    }

    public function testConstructorClampsResolution(): void
    {
        $request = new RasterizeFileRequest(self::$filePath, resolution: random_int(0, PHP_INT_MAX));

        $this->assertGreaterThanOrEqual(RasterizeFileRequest::MIN_RESOLUTION, $request->resolution);
        $this->assertLessThanOrEqual(RasterizeFileRequest::MAX_RESOLUTION, $request->resolution);
    }

    public function testConstructorResolvesOutputDirectoryWhenEmptyOutputDirectoryProvided(): void
    {
        $this->assertNotEmpty(new RasterizeFileRequest(self::$filePath, outputDirectory: null)->outputDirectory);
    }
}
