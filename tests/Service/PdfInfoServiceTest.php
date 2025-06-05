<?php

namespace OneToMany\PdfToImage\Tests\Service;

use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Exception\ReadingPdfMetadataFailedException;
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
        $this->expectException(ReadingPdfMetadataFailedException::class);
        $this->expectExceptionMessageMatches('/Syntax Warning: May not be a PDF file \(continuing anyway\)/');

        new PdfInfoService()->read(__FILE__);
    }

    #[DataProvider('providerFilePathAndPages')]
    public function testReadingPdfInfo(string $filePath, int $pages): void
    {
        $this->assertEquals($pages, new PdfInfoService()->read($filePath)->pages);
    }

    /**
     * @return list<list<int|string>>
     */
    public static function providerFilePathAndPages(): array
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
