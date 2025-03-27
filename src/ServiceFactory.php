<?php

namespace OneToMany\PdfToImage;

use OneToMany\PdfToImage\Exception\InvalidRasterServiceException;
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

        /** @var RasterServiceInterface */
        return $this->container->get($service);
    }

}
