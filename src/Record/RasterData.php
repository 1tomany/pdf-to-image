<?php

namespace OneToMany\PdfToImage\Record;

use function base64_encode;
use function sprintf;

final readonly class RasterData implements \Stringable
{
    public function __construct(
        public string $mimeType,
        public string $dataBytes,
    ) {
    }

    public function __toString(): string
    {
        return $this->dataBytes;
    }

    public function toDataUri(): string
    {
        return sprintf('data:%s;base64,%s', $this->mimeType, base64_encode($this->dataBytes));
    }
}
