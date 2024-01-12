<?php

namespace AaxisTest\Exception;

class InvalidEntityException extends \Exception
{
    public function __construct(private readonly array $failures = [], string $message = '')
    {
        parent::__construct($message, 400);
    }

    public function getErrors(): array
    {
        return $this->failures;
    }
}