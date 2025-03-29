<?php

namespace OneToMany\PdfToImage\Record;

use function base64_encode;
use function sprintf;

final readonly class RasterData implements \Stringable
{

    public function __construct(
        public string $mediaType,
        public string $binaryData,
    )
    {
    }

    public function __toString(): string
    {
        return $this->binaryData;
    }

    public function asUri(): string
    {
        return sprintf('data:%s;base64,%s', $this->mediaType, base64_encode($this->binaryData));
    }

}
