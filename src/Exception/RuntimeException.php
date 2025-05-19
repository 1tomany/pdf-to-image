<?php

namespace OneToMany\PdfToImage\Exception;

class RuntimeException extends \RuntimeException implements ExceptionInterface
{
    public function __construct(string $message = '', ?\Throwable $previous = null, int $code = 0)
    {
        parent::__construct($message, $code, $previous);
    }
}
