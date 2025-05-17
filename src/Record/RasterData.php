<?php

namespace OneToMany\PdfToImage\Record;

use function base64_encode;
use function sprintf;

final readonly class RasterData implements \Stringable
{
    public function __construct(
        public string $type,
        public string $data,
    ) {
    }

    public function __toString(): string
    {
        return $this->data;
    }

    public function toDataUri(): string
    {
        return sprintf('data:%s;base64,%s', $this->type, base64_encode($this->data));
    }
}
