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
    public function testConstructorRequiresValidPdfToPpmBinary(): void
    {
        $binary = 'invalid_pdftoppm_binary';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The binary "'.$binary.'" could not be found.');

        new PopplerRasterService($binary);
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

    #[DataProvider('providerFilePageTypeResolutionAndSha1Hash')]
    public function testRasterizingFirstPage(
        string $file,
        int $page,
        ImageType $type,
        int $resolution,
        string $sha1,
    ): void {
        $request = new RasterizeFileRequest(
            $file, $page, $type, $resolution
        );

        $data = new PopplerRasterService()->rasterize($request);
        $this->assertEquals($sha1, sha1($data->__toString()));
    }

    /**
     * @return list<list<int|string|ImageType>>
     */
    public static function providerFilePageTypeResolutionAndSha1Hash(): array
    {
        $provider = [
            [__DIR__.'/files/pages-1.pdf', 1, ImageType::Jpeg, 150, 'bfbfea39b881befa7e0af249f4fff08592d1ff56'],
            [__DIR__.'/files/pages-2.pdf', 1, ImageType::Jpeg, 300, 'f8b755881dc51980e8a9b4bb147a9c1388f91768'],
            [__DIR__.'/files/pages-2.pdf', 1, ImageType::Png, 150, 'ba1276ebf1e1cbd934e3f9b54a6659678ad4f918'],
            [__DIR__.'/files/pages-3.pdf', 1, ImageType::Jpeg, 72, '5ff2aaa08133b6129371a3f61d96c1522626c974'],
            [__DIR__.'/files/pages-4.pdf', 1, ImageType::Png, 72, 'cd49f354f52895745b94845bfd7261e01a5458d9'],
        ];

        return $provider;
    }
}
