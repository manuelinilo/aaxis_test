<?php

namespace AaxisTest\Exception;

use Particle\Validator\Failure;

class InvalidRequestException extends \Exception
{
    public function __construct(private readonly array $failures = [], private readonly ?string $identifier = null, $message = '')
    {
        parent::__construct($message, 400);
    }

    /**
     * @return Failure[]
     */
    public function getErrors(): array
    {
        return $this->failures;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }
}