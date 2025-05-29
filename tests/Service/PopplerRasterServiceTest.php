<?php

namespace OneToMany\PdfToImage\Tests\Service;

use OneToMany\PdfToImage\Contract\ImageType;
use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Exception\RasterizingPdfFailedException;
use OneToMany\PdfToImage\Request\RasterizeFileRequest;
use OneToMany\PdfToImage\Service\PopplerRasterService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Large;
use PHPUnit\Framework\TestCase;

use function random_int;

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
        $this->expectException(RasterizingPdfFailedException::class);

        new PopplerRasterService()->rasterize(new RasterizeFileRequest(__FILE__));
    }

    public function testRasterizationRequiresValidPageNumber(): void
    {
        $this->expectException(RasterizingPdfFailedException::class);
        $this->expectExceptionMessageMatches('/Wrong page range given/');

        $pageNumber = random_int(2, 100);
        $filePath = __DIR__.'/files/pages-1.pdf';

        new PopplerRasterService()->rasterize(new RasterizeFileRequest($filePath, $pageNumber));
    }

    // #[DataProvider('providerFilePageTypeResolutionAndSha1Hash')]
    public function testRasterizingSinglePage(): void
    {
        $request = new RasterizeFileRequest(
            __DIR__.'/files/pages-1.pdf'
        );

        $images = new PopplerRasterService()->rasterize($request);
    }

    /**
     * @return list<list<int|string|ImageType>>
     */
    public static function providerFilePageTypeResolutionAndSha1Hash(): array
    {
        $provider = [
            [__DIR__.'/files/pages-1.pdf', 1, ImageType::Jpeg, 150, 'bfbfea39b881befa7e0af249f4fff08592d1ff56'],
            [__DIR__.'/files/pages-2.pdf', 1, ImageType::Jpeg, 300, 'b4f24570eaeda3bc0b2865e7583666ec9cae8cc3'],
            [__DIR__.'/files/pages-2.pdf', 1, ImageType::Png, 150, '73ee6b53e3c48945095da187be916593e2cbec17'],
            [__DIR__.'/files/pages-3.pdf', 1, ImageType::Jpeg, 72, '932f94066020ae177c64544c6611441570dc2b50'],
            [__DIR__.'/files/pages-4.pdf', 1, ImageType::Png, 72, 'a074c43375569c0f8d1b24a9fc7dbc456b5c126d'],
        ];

        return $provider;
    }
}
