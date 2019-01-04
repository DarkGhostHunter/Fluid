<?php

namespace DarkGhostHunter\Fluid\Exceptions;

use Exception;
use Throwable;

class InvalidAttributeException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        $message = "Attribute [$message] cannot be set.";

        parent::__construct($message, $code, $previous);
    }
}