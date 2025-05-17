<?php

namespace OneToMany\PdfToImage\Tests\Request;

use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Record\RasterData;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class RasterDataTest extends TestCase
{
    public function testToString(): void
    {
        $text = 'Hello, world!';

        $this->assertEquals($text, new RasterData('text/plain', $text)->__toString());
    }

    public function testToDataUri(): void
    {
        $dataUri = 'data:text/plain;base64,SGVsbG8sIHdvcmxkIQ==';

        $this->assertEquals($dataUri, new RasterData('text/plain', 'Hello, world!')->toDataUri());
    }
}
