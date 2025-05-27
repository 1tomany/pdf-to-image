<?php

namespace OneToMany\PdfToImage\Tests\Helper;

use OneToMany\PdfToImage\Helper\PdfInfo;
use PHPUnit\Framework\TestCase;

final class PdfInfoTest extends TestCase
{
    public function testPdfInfo(): void
    {
        $data = new PdfInfo()->read(__DIR__.'/../Service/files/pages-4.pdf');
        // print_r($data);
    }
}
