<?php

namespace OneToMany\PdfToImage\Tests\Request;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('RequestTests')]
final class RasterizeFileRequestTest extends TestCase
{
    public function testConstructorRequiresFile(): void
    {
    }

    public function testConstructorRequiresReadableFile(): void
    {
    }
}
