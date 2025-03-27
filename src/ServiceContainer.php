<?php

namespace OneToMany\PdfToImage;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final readonly class ServiceContainer implements ContainerInterface
{

    /**
     * @var array<string, RasterServiceInterface>
     */
    private array $services;

    public function __construct()
    {
        $this->services = [
            'mock' => new MockRasterService(),
            'poppler' => new PopplerRasterService(),
        ];
    }

    /**
     * @see Psr\Container\ContainerInterface
     */
    public function get(string $id): RasterServiceInterface
    {
        if (!$this->has($id)) {
            throw new class(sprintf('The service "%s" could not be found.', $id)) extends \InvalidArgumentException implements NotFoundExceptionInterface { };
        }

        return $this->services[$id];
    }

    /**
     * @see Psr\Container\ContainerInterface
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }

}
