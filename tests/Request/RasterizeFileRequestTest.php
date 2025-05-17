<?php

namespace OneToMany\PdfToImage\Tests\Request;

use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

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
}
