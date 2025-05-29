<?php

namespace OneToMany\PdfToImage\Tests\Request;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Record\RasterImage;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class RasterImageTest extends TestCase
{
    public static string $filePath;

    public static function setUpBeforeClass(): void
    {
        self::$filePath = __DIR__.'/files/page-1.jpeg';
    }

    public function testToString(): void
    {
        $this->assertEquals(self::$filePath, new RasterImage(self::$filePath, ImageType::Jpeg, false)->__toString());
    }

    public function testToDataUri(): void
    {
        $this->assertStringStartsWith('data:image/jpeg;base64', new RasterImage(self::$filePath, ImageType::Jpeg, false)->toDataUri());
    }
}
