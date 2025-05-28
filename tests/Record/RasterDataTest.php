<?php

namespace OneToMany\PdfToImage\Tests\Request;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Record\RasterData;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

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
        $dataUri = 'data:image/jpeg;base64,SGVsbG8sIHdvcmxkIQ==';

        $this->assertEquals($dataUri, new RasterData(ImageType::Jpeg, 'Hello, world!')->toDataUri());
    }
}
