<?php

namespace OneToMany\PdfToImage\Tests\Service;

use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Exception\RasterizationFailedException;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use OneToMany\PdfToImage\Service\PopplerRasterService;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function pathinfo;

#[Group('UnitTests')]
#[Group('ServiceTests')]
final class PopplerRasterServiceTest extends TestCase
{
    public function testRasterizationRequiresValidRasterizerPath(): void
    {
        $binary = 'invalid_pdftoppm_binary';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The Poppler binary "'.$binary.'" could not be found.');

        new PopplerRasterService($binary)->rasterize(new RasterizeFileRequest(__FILE__));
    }

    public function testRasterizationRequiresValidPdfFile(): void
    {
        $this->expectException(RasterizationFailedException::class);

        $fileInfo = pathinfo($filePath = __FILE__);
        $this->assertNotEquals('pdf', $fileInfo['extension']);

        new PopplerRasterService()->rasterize(new RasterizeFileRequest($filePath));
    }
}
