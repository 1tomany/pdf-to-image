<?php

namespace OneToMany\PdfToImage\Tests;

use OneToMany\PdfToImage\Exception\InvalidRasterServiceException;
use OneToMany\PdfToImage\ServiceFactory;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('UnitTests')]
final class ServiceFactoryTest extends TestCase
{
    public function testCreatingServiceRequiresServiceToExist(): void
    {
        $this->expectException(InvalidRasterServiceException::class);
        $this->expectExceptionMessage('The raster service "invalid" is invalid.');

        new ServiceFactory(new TestContainer())->create('invalid');
    }

    public function testCreatingServiceRequiresServiceToImplementRasterServiceInterface(): void
    {
        $this->expectException(InvalidRasterServiceException::class);
        $this->expectExceptionMessage('The raster service "error" is invalid.');

        new ServiceFactory(new TestContainer())->create('error');
    }
}
