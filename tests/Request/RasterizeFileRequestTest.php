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

    public function testConstructorRequiresReadableFile(): void
    {
        $filePath = __DIR__.'/invalid.file.path';
        $this->assertFileDoesNotExist($filePath);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The input file "'.$filePath.'" does not exist or is not readable.');

        new RasterizeFileRequest($filePath);
    }

    public function testConstructorRequiresFirstPageToBeLessThanOrEqualToFinalPage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The first page number must be less than or equal to the final page number.');

        new RasterizeFileRequest(self::$filePath, firstPage: random_int(11, 20), finalPage: random_int(1, 10));
    }

    public function testConstructorRequiresNonNullOutputDirectoryToExist(): void
    {
        $outputDirectory = __DIR__.'/invalid/file-directory';
        $this->assertDirectoryDoesNotExist($outputDirectory);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The output directory "'.$outputDirectory.'" does not exist or is not writable.');

        new RasterizeFileRequest(self::$filePath, outputDirectory: $outputDirectory);
    }

    public function testConstructorSetsFormatToJpegByDefault(): void
    {
        $request = new RasterizeFileRequest(self::$filePath);
        $this->assertSame(ImageType::Jpeg, $request->format);
    }

    #[DataProvider('providerFilePathAndPage')]
    public function _testConstructor(string $filePath, int $page): void
    {
        $type = ImageType::cases()[
            array_rand(ImageType::cases())
        ];

        $resolution = random_int(48, 300);

        $request = new RasterizeFileRequest(
            $filePath, $page, $type, $resolution
        );

        $this->assertEquals($filePath, $request->filePath);
        $this->assertEquals($page, $request->firstPage);
        $this->assertEquals($type, $request->format);
        $this->assertEquals($resolution, $request->resolution);
    }

    /**
     * @return list<list<int|string>>
     */
    public static function providerFilePathAndPage(): array
    {
        $provider = [
            [__DIR__.'/files/label.pdf', 1],
        ];

        return $provider;
    }
}
