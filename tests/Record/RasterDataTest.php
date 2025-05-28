<?php

namespace OneToMany\PdfToImage\Tests\Request;

use OneToMany\DataUri\SmartFile;
use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Record\RasterData;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function base64_encode;
use function file_get_contents;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class RasterDataTest extends TestCase
{
    public function testToString(): void
    {
        $text = 'Hello, world!';

        $this->assertEquals($text, new RasterData(ImageType::Jpeg, $text)->__toString());
    }

    public function testToDataUri(): void
    {
        $filePath = __DIR__.'/files/page.png';
        $this->assertFileExists($filePath);

        $bytes = file_get_contents($filePath);

        $this->assertIsString($bytes);
        $this->assertNotEmpty($bytes);

        $dataUri = 'data:image/png;base64,'.base64_encode($bytes);
        $this->assertEquals($dataUri, new RasterData(ImageType::Png, $bytes)->toDataUri());
    }

    public function testToSmartFile(): void
    {
        $filePath = __DIR__.'/files/page.png';
        $this->assertFileExists($filePath);

        $bytes = file_get_contents($filePath);

        $this->assertIsString($bytes);
        $this->assertNotEmpty($bytes);

        $file = new SmartFile($filePath, null, 'image/png', null, null, true, false);

        $data = new RasterData(ImageType::Png, $bytes);
        $this->assertTrue($file->equals($data->toSmartFile()));
    }
}
