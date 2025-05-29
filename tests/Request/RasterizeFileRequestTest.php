<?php

namespace OneToMany\PdfToImage\Tests\Request;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function array_rand;
use function assert;
use function file_exists;
use function random_int;

use const PHP_INT_MAX;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class RasterizeFileRequestTest extends TestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = __DIR__.'/files/label.pdf';

        $this->assertFileExists($this->filePath);
        // assert(file_exists($this->filePath));
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

        new RasterizeFileRequest($this->filePath, firstPage: random_int(11, 20), finalPage: random_int(1, 10));
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMinimumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The resolution must be an integer between 48 and 300.');

        new RasterizeFileRequest($this->filePath, resolution: random_int(1, RasterizeFileRequest::MIN_RESOLUTION+1));
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMaximumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The resolution must be an integer between 48 and 300.');

        new RasterizeFileRequest($this->filePath, resolution: random_int(RasterizeFileRequest::MAX_RESOLUTION+1, PHP_INT_MAX));
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
