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
        $file = __DIR__;

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The input file "'.$file.'" does not exist or is not readable.');

        new RasterizeFileRequest($file);
    }

    public function testConstructorRequiresPositivePageNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The page number must be an integer greater than 0.');

        new RasterizeFileRequest(file: __FILE__, page: 0);
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMinimumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The resolution must be an integer between 48 and 300.');

        new RasterizeFileRequest(file: __FILE__, dpi: random_int(1, 47));
    }

    public function testConstructorRequiresResolutionToBeLessThanOrEqualToMaximumResolution(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The resolution must be an integer between 48 and 300.');

        new RasterizeFileRequest(file: __FILE__, dpi: random_int(301, PHP_INT_MAX));
    }

    public function testConstructor(): void
    {
        $file = __FILE__;
        $page = random_int(1, 100);
        $dpi = random_int(48, 300);

        $type = ImageType::cases()[
            array_rand(ImageType::cases())
        ];

        $request = new RasterizeFileRequest(
            $file, $page, $type, $dpi
        );

        $this->assertEquals($file, $request->file);
        $this->assertEquals($page, $request->page);
        $this->assertSame($type, $request->type);
        $this->assertEquals($dpi, $request->dpi);
    }
}
