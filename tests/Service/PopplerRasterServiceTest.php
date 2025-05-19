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
    public function testRasterizationRequiresValidPdfToPpmBinary(): void
    {
        $pdfToPpmBinary = 'invalid_pdftoppm_binary';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The Poppler binary "'.$pdfToPpmBinary.'" could not be found.');

        new PopplerRasterService(pdftoppmBinary: $pdfToPpmBinary)->rasterize(new RasterizeFileRequest(__FILE__));
    }

    public function testRasterizationRequiresValidPdfFile(): void
    {
        $this->expectException(RasterizationFailedException::class);

        $fileInfo = pathinfo($filePath = __FILE__);
        $this->assertNotEquals('pdf', $fileInfo['extension'] ?? null);

        new PopplerRasterService()->rasterize(new RasterizeFileRequest($filePath));
    }

    public function _testRasterizationRequiresValidPageNumber(): void
    {
        $filePath = __DIR__.'/files/pages-1.pdf';

        $request = new RasterizeFileRequest($filePath, 10);
        new PopplerRasterService()->rasterize($request);
    }
}
