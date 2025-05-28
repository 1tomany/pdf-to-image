<?php

namespace OneToMany\PdfToImage\Record;

use function base64_encode;
use function sprintf;

final readonly class RasterData implements \Stringable
{
    public function __construct(
        public string $contentType,
        public string $bytes,
    ) {
    }

    public function __toString(): string
    {
        return $this->bytes;
    }

    public function toDataUri(): string
    {
        return sprintf('data:%s;base64,%s', $this->contentType, base64_encode($this->bytes));
    }
}
