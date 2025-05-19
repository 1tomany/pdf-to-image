<?php

namespace OneToMany\PdfToImage\Tests\Service;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Exception\RasterizationFailedException;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use OneToMany\PdfToImage\Service\PopplerRasterService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

use function pathinfo;
use function random_int;
use function sha1;

#[Large]
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

    public function testRasterizationRequiresValidPageNumber(): void
    {
        $this->expectException(RasterizationFailedException::class);
        $this->expectExceptionMessageMatches('/Wrong page range given/');

        $pageNumber = random_int(2, 100);
        $filePath = __DIR__.'/files/pages-1.pdf';

        new PopplerRasterService()->rasterize(new RasterizeFileRequest($filePath, $pageNumber));
    }

    #[DataProvider('providerFileNameImageTypeResolutionAndSha1Hash')]
    public function testRasterizingFirstPage(
        string $fileName,
        ImageType $imageType,
        int $resolution,
        string $sha1Hash,
    ): void {
        $filePath = __DIR__.'/files/'.$fileName;

        $request = new RasterizeFileRequest(...[
            'filePath' => $filePath,
            'imageType' => $imageType,
            'resolution' => $resolution,
        ]);

        $this->assertEquals(1, $request->pageNumber);

        $data = new PopplerRasterService()->rasterize($request);
        $this->assertEquals($sha1Hash, sha1($data->__toString()));
    }

    /**
     * @return list<list<int|string|ImageType>>
     */
    public static function providerFileNameImageTypeResolutionAndSha1Hash(): array
    {
        $provider = [
            ['pages-1.pdf', ImageType::Jpeg, 150, 'bfbfea39b881befa7e0af249f4fff08592d1ff56'],
            ['pages-2.pdf', ImageType::Jpeg, 300, 'f8b755881dc51980e8a9b4bb147a9c1388f91768'],
            ['pages-2.pdf', ImageType::Png, 150, 'ba1276ebf1e1cbd934e3f9b54a6659678ad4f918'],
            ['pages-3.pdf', ImageType::Jpeg, 72, '5ff2aaa08133b6129371a3f61d96c1522626c974'],
            ['pages-4.pdf', ImageType::Png, 72, 'cd49f354f52895745b94845bfd7261e01a5458d9'],
        ];

        return $provider;
    }
}
