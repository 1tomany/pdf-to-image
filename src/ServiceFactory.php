<?php

namespace OneToMany\PdfToImage;

use OneToMany\PdfToImage\Exception\InvalidRasterServiceException;
use OneToMany\PdfToImage\Service\RasterServiceInterface;
use Psr\Container\ContainerInterface;

final readonly class ServiceFactory
{
    public function __construct(private ContainerInterface $container)
    {
    }

    public function create(string $service): RasterServiceInterface
    {
        if (!$this->container->has($service)) {
            throw new InvalidRasterServiceException($service);
        }

        $rasterService = $this->container->get($service);

        if (!$rasterService instanceof RasterServiceInterface) {
            throw new InvalidRasterServiceException($service);
        }

        return $rasterService;
    }
}
