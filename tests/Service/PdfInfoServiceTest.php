<?php

namespace OneToMany\PdfToImage\Tests\Service;

use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Exception\ReadingPdfInfoFailedException;
use OneToMany\PdfToImage\Service\PdfInfoService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
#[Group('ServiceTests')]
final class PdfInfoServiceTest extends TestCase
{
    public function testConstructorRequiresValidPdfInfoBinary(): void
    {
        $binary = 'invalid_pdfinfo_binary';

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The binary "'.$binary.'" could not be found.');

        new PdfInfoService($binary);
    }

    public function testReadingInfoRequiresValidPdfFile(): void
    {
        $this->expectException(ReadingPdfInfoFailedException::class);

        new PdfInfoService()->read(__FILE__);
    }

    #[DataProvider('providerFileAndPages')]
    public function testReadingPdfInfo(string $file, int $pages): void
    {
        $pdfInfo = new PdfInfoService()->read($file);
        $this->assertEquals($pages, $pdfInfo->pages);
    }

    /**
     * @return list<list<int|string>>
     */
    public static function providerFileAndPages(): array
    {
        $provider = [
            [__DIR__.'/files/pages-1.pdf', 1],
            [__DIR__.'/files/pages-2.pdf', 2],
            [__DIR__.'/files/pages-3.pdf', 3],
            [__DIR__.'/files/pages-4.pdf', 4],
        ];

        return $provider;
    }
}
