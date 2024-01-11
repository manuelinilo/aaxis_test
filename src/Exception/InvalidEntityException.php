<?php

namespace App\Exception;

use Particle\Validator\Failure;

class InvalidEntityException extends \Exception
{
    public function __construct(private readonly array $failures = [], $message = '')
    {
        parent::__construct($message, 400);
    }

    public function getErrors(): array
    {
        return $this->failures;
    }
}