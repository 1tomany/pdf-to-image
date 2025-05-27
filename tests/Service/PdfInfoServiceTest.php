<?php

namespace OneToMany\PdfToImage\Tests\Service;

use OneToMany\PdfToImage\Service\PdfInfoService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PdfInfoServiceTest extends TestCase
{
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
