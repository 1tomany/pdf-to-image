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
    public function testConstructorRequiresReadableFile(): void
    {
        $filePath = __DIR__.'/invalid.file.path';
        $this->assertFileDoesNotExist($filePath);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The input file "'.$filePath.'" does not exist or is not readable.');

        new RasterizeFileRequest($filePath);
    }

    public function testConstructorRequiresPositivePageNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The page number must be a positive non-zero integer.');

        new RasterizeFileRequest(path: __FILE__, page: 0);
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMinimumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The resolution must be an integer between 48 and 300.');

        new RasterizeFileRequest(path: __FILE__, resolution: random_int(1, 47));
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMaximumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The resolution must be an integer between 48 and 300.');

        new RasterizeFileRequest(path: __FILE__, resolution: random_int(301, PHP_INT_MAX));
    }

    #[DataProvider('providerFilePathAndPage')]
    public function testConstructor(string $filePath, int $page): void
    {
        $type = ImageType::cases()[
            array_rand(ImageType::cases())
        ];

        $resolution = random_int(48, 300);

        $request = new RasterizeFileRequest(
            $filePath, $page, $type, $resolution
        );

        $this->assertEquals($filePath, $request->path);
        $this->assertEquals($page, $request->page);
        $this->assertEquals($type, $request->type);
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
