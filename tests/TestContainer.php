<?php

namespace OneToMany\PdfToImage\Tests;

use OneToMany\PdfToImage\Exception\InvalidArgumentException;
use OneToMany\PdfToImage\Service\MockRasterService;
use Psr\Container\ContainerInterface;

final readonly class TestContainer implements ContainerInterface
{
    private array $services;

    public function __construct()
    {
        $this->services = [
            'mock' => new MockRasterService(),
            'error' => new InvalidArgumentException(),
        ];
    }

    /**
     * @see Psr\Container\ContainerInterface
     */
    public function get(string $id): mixed
    {
        return $this->services[$id] ?? null;
    }

    /**
     * @see Psr\Container\ContainerInterface
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }
}
