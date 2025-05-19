<?php

namespace OneToMany\PdfToImage\Tests\Request;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
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
        $filePath = __DIR__;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The input file "'.$filePath.'" does not exist or is not readable.');

        new RasterizeFileRequest($filePath);
    }

    public function testConstructorRequiresPositivePageNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The page number must be an integer greater than 0.');

        new RasterizeFileRequest(filePath: __FILE__, pageNumber: 0);
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMinimumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The resolution must be an integer between 48 and 300.');

        new RasterizeFileRequest(filePath: __FILE__, resolution: random_int(1, 47));
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMaximumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The resolution must be an integer between 48 and 300.');

        new RasterizeFileRequest(filePath: __FILE__, resolution: random_int(301, PHP_INT_MAX));
    }

    public function testConstructor(): void
    {
        $filePath = __FILE__;
        $pageNumber = random_int(1, 100);
        $resolution = random_int(48, 300);

        $imageType = ImageType::cases()[
            array_rand(ImageType::cases())
        ];

        $request = new RasterizeFileRequest(...[
            'filePath' => $filePath,
            'pageNumber' => $pageNumber,
            'imageType' => $imageType,
            'resolution' => $resolution,
        ]);

        $this->assertEquals($filePath, $request->filePath);
        $this->assertEquals($pageNumber, $request->pageNumber);
        $this->assertSame($imageType, $request->imageType);
        $this->assertEquals($resolution, $request->resolution);
    }
}
